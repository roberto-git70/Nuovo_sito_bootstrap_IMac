const fs = require('fs');
const path = require('path');

const projectRoot = __dirname;
const htmlFiles = fs.readdirSync(projectRoot).filter(file => file.endsWith('.html'));

console.log('🟡 Verifica dei link JS negli HTML...\n');

htmlFiles.forEach(file => {
  const filePath = path.join(projectRoot, file);
  const content = fs.readFileSync(filePath, 'utf-8');
  const regex = /<script[^>]*src=["']([^"']+\.js)["'][^>]*><\/script>/gi;

  let match;
  while ((match = regex.exec(content)) !== null) {
    const jsPath = match[1].replace(/^\.?\//, ''); // rimuove ./ o / iniziale
    const fullJsPath = path.join(projectRoot, jsPath);

    if (!fs.existsSync(fullJsPath)) {
      console.log(`❌ MANCANTE: ${match[1]} (referenziato in ${file})`);
    } else {
      console.log(`✅ OK: ${match[1]} (da ${file})`);
    }
  }
});