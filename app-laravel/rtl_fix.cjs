const fs = require('fs');
const path = require('path');

const directories = [
    'd:/Work/tafseer/tafseer/app-laravel/resources/views/mosabka/judgings/quran',
    'd:/Work/tafseer/tafseer/app-laravel/resources/views/mosabka/judgings/tafseer'
];

const patterns = [
    { regex: /\bml-(\d+|auto|px|0\.5|1\.5|2\.5|3\.5)\b/g, replacement: 'ms-$1' },
    { regex: /\bmr-(\d+|auto|px|0\.5|1\.5|2\.5|3\.5)\b/g, replacement: 'me-$1' },
    { regex: /\bpl-(\d+|px|0\.5|1\.5|2\.5|3\.5)\b/g, replacement: 'ps-$1' },
    { regex: /\bpr-(\d+|px|0\.5|1\.5|2\.5|3\.5)\b/g, replacement: 'pe-$1' },
    { regex: /\btext-left\b/g, replacement: 'text-start' },
    { regex: /\btext-right\b/g, replacement: 'text-end' }
];

function processFile(filePath) {
    if (filePath.endsWith('.bak')) return;
    
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;
    
    patterns.forEach(p => {
        content = content.replace(p.regex, p.replacement);
    });
    
    if (content !== original) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Updated: ${filePath}`);
    }
}

directories.forEach(dir => {
    if (fs.existsSync(dir)) {
        const files = fs.readdirSync(dir);
        files.forEach(file => {
            if (file.endsWith('.blade.php')) {
                processFile(path.join(dir, file));
            }
        });
    }
});

console.log('RTL optimization complete.');
