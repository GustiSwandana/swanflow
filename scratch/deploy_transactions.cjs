const fs = require('fs');
const path = require('path');

const uploadUrl = 'https://srv1762-files.hstgr.io/rest/063eff4355db13a9/api/tus/public_html';
const authKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTI5OTAyNDQ0NyIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODk5NzgzNiwiaWF0IjoxNzg4OTc2MjM2fQ.T2nEJXu8fPvCX7HlKimgrvvXxel3K_3UH_-AELYFJIM';
const restAuthKey = '0cb82203458ba6c6c9ee4b5f11861a6585cc9005b68a580ca264602cbc947568-063eff4355db13a9';

const LOCAL_ROOT = 'c:/laragon/www/swanflow';

const filesToUpload = [
    // CSS tokens
    { local: 'resources/css/app.css', remote: 'swanflow/resources/css/app.css' },

    // Layout
    { local: 'resources/views/layouts/mobile.blade.php', remote: 'swanflow/resources/views/layouts/mobile.blade.php' },

    // Main views
    { local: 'resources/views/dashboard.blade.php', remote: 'swanflow/resources/views/dashboard.blade.php' },
    { local: 'resources/views/transactions/index.blade.php', remote: 'swanflow/resources/views/transactions/index.blade.php' },
    { local: 'resources/views/drive/index.blade.php', remote: 'swanflow/resources/views/drive/index.blade.php' },
    { local: 'resources/views/drive/drop.blade.php', remote: 'swanflow/resources/views/drive/drop.blade.php' },
    { local: 'resources/views/drive/share.blade.php', remote: 'swanflow/resources/views/drive/share.blade.php' },
    { local: 'resources/views/todos/index.blade.php', remote: 'swanflow/resources/views/todos/index.blade.php' },
    { local: 'resources/views/todos/_card.blade.php', remote: 'swanflow/resources/views/todos/_card.blade.php' },
    { local: 'resources/views/todos/_empty.blade.php', remote: 'swanflow/resources/views/todos/_empty.blade.php' },
    { local: 'resources/views/wallets/index.blade.php', remote: 'swanflow/resources/views/wallets/index.blade.php' },
    { local: 'resources/views/reports/index.blade.php', remote: 'swanflow/resources/views/reports/index.blade.php' },
    { local: 'resources/views/budgets/index.blade.php', remote: 'swanflow/resources/views/budgets/index.blade.php' },
    { local: 'resources/views/investments/index.blade.php', remote: 'swanflow/resources/views/investments/index.blade.php' },
    { local: 'resources/views/calculator/index.blade.php', remote: 'swanflow/resources/views/calculator/index.blade.php' },
    { local: 'resources/views/subscriptions/index.blade.php', remote: 'swanflow/resources/views/subscriptions/index.blade.php' },
    { local: 'resources/views/debts/index.blade.php', remote: 'swanflow/resources/views/debts/index.blade.php' },
    { local: 'resources/views/categories/index.blade.php', remote: 'swanflow/resources/views/categories/index.blade.php' },
    { local: 'resources/views/profile/edit.blade.php', remote: 'swanflow/resources/views/profile/edit.blade.php' },

    // Build manifest (both public_html/build and public_html/swanflow/public/build)
    { local: 'public/build/manifest.json', remote: 'build/manifest.json' },
    { local: 'public/build/manifest.json', remote: 'swanflow/public/build/manifest.json' },

    // Compiled assets
    { local: 'public/build/assets/app-FfCLdI36.css', remote: 'build/assets/app-FfCLdI36.css' },
    { local: 'public/build/assets/app-FfCLdI36.css', remote: 'swanflow/public/build/assets/app-FfCLdI36.css' },
    { local: 'public/build/assets/fonts-C9MNnjVw.css', remote: 'build/assets/fonts-C9MNnjVw.css' },
    { local: 'public/build/assets/fonts-C9MNnjVw.css', remote: 'swanflow/public/build/assets/fonts-C9MNnjVw.css' },
    { local: 'public/build/assets/app-BvRk9kiK.js', remote: 'build/assets/app-BvRk9kiK.js' },
    { local: 'public/build/assets/app-BvRk9kiK.js', remote: 'swanflow/public/build/assets/app-BvRk9kiK.js' },
];

async function uploadFile(localPath, remotePath) {
    const fullLocalPath = path.resolve(LOCAL_ROOT, localPath);
    if (!fs.existsSync(fullLocalPath)) {
        console.warn(`⚠ Skipped (not found): ${localPath}`);
        return false;
    }
    const fileBuffer = fs.readFileSync(fullLocalPath);
    const size = fileBuffer.length;
    const targetUrl = `${uploadUrl}/${remotePath}?override=true`;

    process.stdout.write(`Uploading ${localPath} -> ${remotePath} (${size}B)... `);

    for (let attempt = 1; attempt <= 3; attempt++) {
        try {
            const postRes = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'X-Auth': authKey,
                    'X-Auth-Rest': restAuthKey,
                    'Tus-Resumable': '1.0.0',
                    'Upload-Length': size.toString(),
                    'Upload-Offset': '0'
                },
                signal: AbortSignal.timeout(20000)
            });

            if (postRes.status === 201 || postRes.status === 200 || postRes.status === 204) {
                const patchRes = await fetch(targetUrl, {
                    method: 'PATCH',
                    headers: {
                        'X-Auth': authKey,
                        'X-Auth-Rest': restAuthKey,
                        'Tus-Resumable': '1.0.0',
                        'Upload-Offset': '0',
                        'Content-Type': 'application/offset+octet-stream',
                        'Content-Length': size.toString()
                    },
                    body: fileBuffer,
                    signal: AbortSignal.timeout(60000)
                });

                if (patchRes.status === 204 || patchRes.status === 200 || patchRes.status === 201) {
                    console.log('✓ OK');
                    return true;
                } else {
                    console.log(`PATCH failed (${patchRes.status}), retrying...`);
                }
            } else {
                console.log(`POST failed (${postRes.status}), retrying...`);
            }
        } catch (e) {
            console.log(`Error: ${e.message}, retrying...`);
        }
        await new Promise(r => setTimeout(r, 1500));
    }
    console.log('❌ FAILED');
    return false;
}

async function main() {
    console.log(`Starting deployment of ${filesToUpload.length} files to swanflow.space...`);
    let successCount = 0;
    let failCount = 0;

    for (const item of filesToUpload) {
        const ok = await uploadFile(item.local, item.remote);
        if (ok) successCount++;
        else failCount++;
    }

    console.log(`\nDeployment summary: ${successCount} succeeded, ${failCount} failed.`);
    if (failCount === 0) {
        console.log('🎉 ALL FILES DEPLOYED SUCCESSFULLY TO SWANFLOW.SPACE!');
    }
}

main().catch(console.error);
