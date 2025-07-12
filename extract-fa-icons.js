const fs = require("fs");
const path = require("path");

const directory = "./"; // Puoi cambiarlo con la directory dove sono gli HTML/JS
const extensions = [".html", ".js"];
const faClasses = new Set();

function scanFiles(dir) {
  fs.readdirSync(dir).forEach(file => {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);

    if (stat.isDirectory() && file !== "node_modules") {
      scanFiles(fullPath);
    } else if (extensions.includes(path.extname(fullPath))) {
      const content = fs.readFileSync(fullPath, "utf8");
      const matches = content.match(/fa-[\w-]+/g);
      if (matches) {
        matches.forEach(cls => faClasses.add(cls));
      }
    }
  });
}

scanFiles(directory);

// Stampa a video e salva su file
const sorted = [...faClasses].sort();
console.log("Icone Font Awesome trovate:\n", sorted.join("\n"));
fs.writeFileSync("fa-icons-used.txt", sorted.join("\n"), "utf8");