<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class PublicGameController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $entrypointPath = $this->findEntrypointFile(public_path('uploaded-games/' . $slug));
        if (! $entrypointPath) {
            abort(404);
        }

        $relativePath = str_replace([public_path() . DIRECTORY_SEPARATOR, '\\'], ['', '/'], $entrypointPath);
        $gameUrl = asset($relativePath);
        $backUrl = auth()->check() ? route('accueil') : route('accueil.invite');

        return view('jeux.uploaded', compact('game', 'gameUrl', 'backUrl'));
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
            if (in_array($fileName, ['index.html', 'jeux.html'], true)) {
                return $fileInfo->getPathname();
            }
        }

        return null;
    }
}