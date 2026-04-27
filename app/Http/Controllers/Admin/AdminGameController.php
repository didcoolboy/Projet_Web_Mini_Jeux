<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\LastPlayed;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class AdminGameController extends Controller
{
    private const MAX_ARCHIVE_BYTES = 25 * 1024 * 1024;
    private const MAX_UNCOMPRESSED_BYTES = 80 * 1024 * 1024;
    private const MAX_FILES_IN_ARCHIVE = 300;

    private const FORBIDDEN_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'phar',
        'exe', 'bat', 'cmd', 'com', 'sh', 'ps1',
        'js.map',
    ];

    private const ENTRYPOINT_NAMES = ['index.html', 'jeux.html'];

    public function index()
    {
        $games = Game::latest()->paginate(20);
        return view('admin.games.index', compact('games'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'zip_file' => 'required|file|mimes:zip,rar|max:25600',
        ]);

        $archiveType = strtolower((string) $request->file('zip_file')->getClientOriginalExtension());
        if (! in_array($archiveType, ['zip', 'rar'], true)) {
            return back()->withInput()->with('error', 'Format non supporte. Utilisez un fichier .zip ou .rar.');
        }

        if ($archiveType === 'zip' && ! class_exists(ZipArchive::class)) {
            return back()->withInput()->with('error', 'Le support ZIP n\'est pas disponible sur le serveur.');
        }

        if ($archiveType === 'rar' && ! class_exists('RarArchive')) {
            return back()->withInput()->with('error', 'Le serveur ne peut pas lire les .rar pour le moment. Recompresse le jeu en .zip et reessaie.');
        }

        $slug = $this->generateUniqueSlug($data['name']);
        $uploadPath = $request->file('zip_file')->getRealPath();

        if (! $uploadPath || ! is_file($uploadPath)) {
            return back()->withInput()->with('error', 'Archive invalide.');
        }

        [$isValid, $errorMessage] = $this->validateArchive($uploadPath, $archiveType);
        if (! $isValid) {
            return back()->withInput()->with('error', $errorMessage);
        }

        $targetDir = public_path('uploaded-games/' . $slug);
        File::deleteDirectory($targetDir);
        File::ensureDirectoryExists($targetDir);

        $extracted = $this->extractArchive($uploadPath, $targetDir, $archiveType);
        if (! $extracted) {
            File::deleteDirectory($targetDir);
            return back()->withInput()->with('error', 'Impossible d\'extraire l\'archive.');
        }

        $entrypointPath = $this->findEntrypointFile($targetDir);
        if (! $entrypointPath) {
            File::deleteDirectory($targetDir);
            return back()->withInput()->with('error', 'L\'archive doit contenir index.html ou jeux.html, meme dans un sous-dossier.');
        }

        Game::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'icon' => '🎮',
        ]);

        return back()->with('success', 'Jeu ajoute avec succes.');
    }

    public function destroy(Game $game)
    {
        $uploadedGameDir = public_path('uploaded-games/' . $game->slug);
        if (File::isDirectory($uploadedGameDir)) {
            File::deleteDirectory($uploadedGameDir);
        }

        // Sécurité supplémentaire: nettoyage explicite des données liées.
        Score::query()->where('game_id', $game->id)->delete();
        LastPlayed::query()->where('game_id', $game->id)->delete();

        $game->delete();
        return back()->with('success', 'Jeu retire.');
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        if ($baseSlug === '') {
            $baseSlug = 'jeu';
        }

        $slug = $baseSlug;
        $counter = 2;
        while (Game::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function validateArchive(string $archivePath, string $archiveType): array
    {
        if (filesize($archivePath) > self::MAX_ARCHIVE_BYTES) {
            return [false, 'L\'archive depasse la taille maximale autorisee (25MB).'];
        }

        return $archiveType === 'rar'
            ? $this->validateRarArchive($archivePath)
            : $this->validateZipArchive($archivePath);
    }

    private function validateZipArchive(string $archivePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($archivePath) !== true) {
            return [false, 'Archive ZIP invalide.'];
        }

        $totalUncompressed = 0;
        $filesCount = 0;
        $hasEntrypoint = false;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (! $stat || ! isset($stat['name'])) {
                $zip->close();
                return [false, 'Archive corrompue.'];
            }

            $entryName = str_replace('\\', '/', $stat['name']);
            if ($entryName === '' || str_contains($entryName, '../') || str_starts_with($entryName, '/')) {
                $zip->close();
                return [false, 'L\'archive contient des chemins non autorises.'];
            }

            if (str_ends_with($entryName, '/')) {
                continue;
            }

            $filesCount++;
            $totalUncompressed += (int) ($stat['size'] ?? 0);

            if ($filesCount > self::MAX_FILES_IN_ARCHIVE) {
                $zip->close();
                return [false, 'L\'archive contient trop de fichiers (max 300).'];
            }

            if ($totalUncompressed > self::MAX_UNCOMPRESSED_BYTES) {
                $zip->close();
                return [false, 'Le contenu decompresse est trop volumineux (max 80MB).'];
            }

            $extension = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));
            if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
                $zip->close();
                return [false, 'L\'archive contient un type de fichier interdit: .' . $extension];
            }

            if (in_array(strtolower(basename($entryName)), self::ENTRYPOINT_NAMES, true)) {
                $hasEntrypoint = true;
            }
        }

        $zip->close();

        if (! $hasEntrypoint) {
            return [false, 'L\'archive doit contenir index.html ou jeux.html.'];
        }

        return [true, null];
    }

    private function validateRarArchive(string $archivePath): array
    {
        $rar = \RarArchive::open($archivePath);
        if (! $rar) {
            return [false, 'Archive RAR invalide.'];
        }

        $entries = $rar->getEntries();
        if (! is_array($entries)) {
            $rar->close();
            return [false, 'Archive RAR corrompue.'];
        }

        $totalUncompressed = 0;
        $filesCount = 0;
        $hasEntrypoint = false;

        foreach ($entries as $entry) {
            $entryName = str_replace('\\', '/', (string) $entry->getName());
            if ($entryName === '' || str_contains($entryName, '../') || str_starts_with($entryName, '/')) {
                $rar->close();
                return [false, 'L\'archive contient des chemins non autorises.'];
            }

            if (str_ends_with($entryName, '/')) {
                continue;
            }

            $filesCount++;
            $totalUncompressed += (int) $entry->getUnpackedSize();

            if ($filesCount > self::MAX_FILES_IN_ARCHIVE) {
                $rar->close();
                return [false, 'L\'archive contient trop de fichiers (max 300).'];
            }

            if ($totalUncompressed > self::MAX_UNCOMPRESSED_BYTES) {
                $rar->close();
                return [false, 'Le contenu decompresse est trop volumineux (max 80MB).'];
            }

            $extension = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));
            if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
                $rar->close();
                return [false, 'L\'archive contient un type de fichier interdit: .' . $extension];
            }

            if (in_array(strtolower(basename($entryName)), self::ENTRYPOINT_NAMES, true)) {
                $hasEntrypoint = true;
            }
        }

        $rar->close();

        if (! $hasEntrypoint) {
            return [false, 'L\'archive doit contenir index.html ou jeux.html.'];
        }

        return [true, null];
    }

    private function extractArchive(string $archivePath, string $targetDir, string $archiveType): bool
    {
        if ($archiveType === 'rar') {
            $rar = \RarArchive::open($archivePath);
            if (! $rar) {
                return false;
            }

            $entries = $rar->getEntries();
            if (! is_array($entries)) {
                $rar->close();
                return false;
            }

            foreach ($entries as $entry) {
                $entryName = str_replace('\\', '/', (string) $entry->getName());
                if ($entryName === '' || str_contains($entryName, '../') || str_starts_with($entryName, '/')) {
                    $rar->close();
                    return false;
                }

                if (! $entry->extract($targetDir)) {
                    $rar->close();
                    return false;
                }
            }

            $rar->close();
            return true;
        }

        $zip = new ZipArchive();
        if ($zip->open($archivePath) !== true) {
            return false;
        }

        $ok = $zip->extractTo($targetDir);
        $zip->close();

        return $ok;
    }

    private function findEntrypointFile(string $targetDir): ?string
    {
        $directCandidates = [
            $targetDir . DIRECTORY_SEPARATOR . 'index.html',
            $targetDir . DIRECTORY_SEPARATOR . 'jeux.html',
        ];

        foreach ($directCandidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($targetDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            $fileName = strtolower($fileInfo->getFilename());
            if (in_array($fileName, self::ENTRYPOINT_NAMES, true)) {
                return $fileInfo->getPathname();
            }
        }

        return null;
    }
}