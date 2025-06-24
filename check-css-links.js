const fs = require('fs');
const path = require('path');

const projectRoot = __dirname;
const htmlFiles = fs.readdirSync(projectRoot).filter(file => file.endsWith('.html'));

console.log('🔍 Verifica dei link CSS negli HTML...\n');

htmlFiles.forEach(file => {
  const filePath = path.join(projectRoot, file);
  const content = fs.readFileSync(filePath, 'utf-8');
  const regex = /<link[^>]*href=["']([^"']+\.css)["']/gi;

  let match;
  while ((match = regex.exec(content)) !== null) {
    const cssPath = match[1].replace(/^\.\//, ''); // rimuove ./ iniziale se presente
    const fullCssPath = path.join(projectRoot, cssPath);

    if (!fs.existsSync(fullCssPath)) {
      console.log(`❌ MANCANTE: ${cssPath} (referenziato in ${file})`);
    } else {
      console.log(`✅ OK: ${cssPath} (da ${file})`);
    }
  }
});
