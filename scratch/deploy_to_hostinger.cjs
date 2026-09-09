const fs = require('fs');
const path = require('path');

const uploadUrl = 'https://srv1762-files.hstgr.io/rest/7e9432e5b7af72d1/api/tus/public_html';
const authKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyIjp7ImlkIjoxLCJsb2NhbGUiOiJlbl9VUyIsInZpZXdNb2RlIjoibGlzdCIsInNpbmdsZUNsaWNrIjpmYWxzZSwicmVkaXJlY3RBZnRlckNvcHlNb3ZlIjpmYWxzZSwicGVybSI6eyJhZG1pbiI6ZmFsc2UsImV4ZWN1dGUiOmZhbHNlLCJjcmVhdGUiOnRydWUsInJlbmFtZSI6dHJ1ZSwibW9kaWZ5Ijp0cnVlLCJkZWxldGUiOnRydWUsInNoYXJlIjpmYWxzZSwiZG93bmxvYWQiOnRydWV9LCJjb21tYW5kcyI6W10sImxvY2tQYXNzd29yZCI6dHJ1ZSwiaGlkZURvdGZpbGVzIjpmYWxzZSwiZGF0ZUZvcm1hdCI6ZmFsc2UsInVzZXJuYW1lIjoidTI5OTAyNDQ0NyIsImFjZUVkaXRvclRoZW1lIjoiIn0sImlzcyI6IkZpbGUgQnJvd3NlciIsImV4cCI6MTc4ODk1MDM4MCwiaWF0IjoxNzg4OTI4NzgwfQ.UdvO4wSSJsOCJISCD7bmAvqZ-uMFZDzmLH_LWN6SBOg';
const restAuthKey = '146bba8d6e5bb3d0711041509879e09f404c67630956ca7c7eb3d396dbdb3fdc-7e9432e5b7af72d1';

const LOCAL_ROOT = 'c:/laragon/www/swanflow';

const filesToUpload = [
    // Views
    { local: 'resources/views/layouts/mobile.blade.php', remote: 'swanflow/resources/views/layouts/mobile.blade.php' },
    { local: 'resources/views/dashboard.blade.php', remote: 'swanflow/resources/views/dashboard.blade.php' },
    { local: 'resources/views/todos/index.blade.php', remote: 'swanflow/resources/views/todos/index.blade.php' },
    { local: 'resources/views/reports/index.blade.php', remote: 'swanflow/resources/views/reports/index.blade.php' },
    { local: 'resources/views/calculator/index.blade.php', remote: 'swanflow/resources/views/calculator/index.blade.php' },
    { local: 'resources/views/drive/index.blade.php', remote: 'swanflow/resources/views/drive/index.blade.php' },

    { local: 'resources/views/todos/_card.blade.php', remote: 'swanflow/resources/views/todos/_card.blade.php' },

    // Build assets (manifest)
    { local: 'public/build/manifest.json', remote: 'build/manifest.json' },
    { local: 'public/build/manifest.json', remote: 'swanflow/public/build/manifest.json' },

    // Build assets (CSS & JS)
    { local: 'public/build/assets/app-D0Sh87EC.css', remote: 'build/assets/app-D0Sh87EC.css' },
    { local: 'public/build/assets/app-D0Sh87EC.css', remote: 'swanflow/public/build/assets/app-D0Sh87EC.css' },
    { local: 'public/build/assets/app-BvRk9kiK.js', remote: 'build/assets/app-BvRk9kiK.js' },
    { local: 'public/build/assets/app-BvRk9kiK.js', remote: 'swanflow/public/build/assets/app-BvRk9kiK.js' },

    // PWA webmanifest & json (both roots)
    { local: 'public/manifest.webmanifest', remote: 'manifest.webmanifest' },
    { local: 'public/manifest.webmanifest', remote: 'swanflow/public/manifest.webmanifest' },
    { local: 'public/manifest.json', remote: 'manifest.json' },
    { local: 'public/manifest.json', remote: 'swanflow/public/manifest.json' },

    // iPhone 15 & iOS Splash PNG screens (both roots)
    { local: 'public/icons/splash/splash-1179x2556.png', remote: 'icons/splash/splash-1179x2556.png' },
    { local: 'public/icons/splash/splash-1179x2556.png', remote: 'swanflow/public/icons/splash/splash-1179x2556.png' },
    { local: 'public/icons/splash/splash-1290x2796.png', remote: 'icons/splash/splash-1290x2796.png' },
    { local: 'public/icons/splash/splash-1290x2796.png', remote: 'swanflow/public/icons/splash/splash-1290x2796.png' },
    { local: 'public/icons/splash/splash-1170x2532.png', remote: 'icons/splash/splash-1170x2532.png' },
    { local: 'public/icons/splash/splash-1170x2532.png', remote: 'swanflow/public/icons/splash/splash-1170x2532.png' },
    { local: 'public/icons/splash/splash-1284x2778.png', remote: 'icons/splash/splash-1284x2778.png' },
    { local: 'public/icons/splash/splash-1284x2778.png', remote: 'swanflow/public/icons/splash/splash-1284x2778.png' },
    { local: 'public/icons/splash/splash-750x1334.png',  remote: 'icons/splash/splash-750x1334.png' },
    { local: 'public/icons/splash/splash-750x1334.png',  remote: 'swanflow/public/icons/splash/splash-750x1334.png' },
];

async function uploadFile(localPath, remotePath) {
    const fullLocalPath = path.resolve(LOCAL_ROOT, localPath);
    if (!fs.existsSync(fullLocalPath)) {
        console.warn(`⚠ Skipped (not found): ${localPath}`);
        return true;
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
                signal: AbortSignal.timeout(15000)
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
