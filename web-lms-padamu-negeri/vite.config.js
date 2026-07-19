import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import { resolve, basename } from 'path';
import { renameSync, existsSync } from 'fs';

/**
 * Vite plugin: rename .mjs assets to .js after build.
 *
 * Nginx/Apache sering tidak punya mapping MIME type untuk .mjs,
 * sehingga file dikirim sebagai application/octet-stream — browser
 * menolak memuatnya sebagai JavaScript module (PDF.js worker gagal).
 * Plugin ini me-rename file .mjs → .js dan memperbaiki referensi
 * di manifest.json supaya semuanya konsisten.
 */
function renameMjsToJs() {
    return {
        name: 'rename-mjs-to-js',
        enforce: 'post',
        apply: 'build',
        generateBundle(_options, bundle) {
            const toRename = [];
            for (const [fileName, chunk] of Object.entries(bundle)) {
                if (fileName.endsWith('.mjs')) {
                    const newName = fileName.replace(/\.mjs$/, '.js');
                    chunk.fileName = newName;
                    toRename.push({ old: fileName, new: newName });
                }
            }
            // Update references inside JS chunks that point to renamed .mjs files
            for (const chunk of Object.values(bundle)) {
                if (chunk.type === 'chunk' && chunk.code) {
                    for (const renamed of toRename) {
                        const oldBase = basename(renamed.old);
                        const newBase = basename(renamed.new);
                        chunk.code = chunk.code.replaceAll(oldBase, newBase);
                    }
                }
            }
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
        renameMjsToJs(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
