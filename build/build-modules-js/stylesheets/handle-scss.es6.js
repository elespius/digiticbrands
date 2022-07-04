const Autoprefixer = require('autoprefixer');
const CssNano = require('cssnano');
const { writeFile } = require('fs').promises;
const { ensureDir } = require('fs-extra');
const { dirname, sep } = require('path');
const Postcss = require('postcss');
const Sass = require('sass');

module.exports.handleScssFile = async (file) => {
  const cssFile = file.replace(`${sep}scss${sep}`, `${sep}css${sep}`)
    .replace(`${sep}build${sep}media_source${sep}`, `${sep}media${sep}`)
    .replace('.scss', '.css');

  let compiled;
  try {
    compiled = Sass.renderSync({ file });
  } catch (error) {
    // eslint-disable-next-line no-console
    console.error(error.formatted);
    process.exit(1);
  }

  // Ensure the folder exists or create it
  await ensureDir(dirname(cssFile), {});
  // Ensure the folder exists or create it
  await ensureDir(dirname(cssFile.replace('.css', '.min.css')), {});

  // Auto prefixing
  const cleaner = Postcss([Autoprefixer()]);
  cleaner.process(compiled.css.toString(), { from: undefined })
    .then((res) => {
      writeFile(cssFile, res.css, { encoding: 'utf8', mode: 0o644 });
      return Postcss([CssNano]).process(res.css, { from: undefined });
    })
    .then((cssMin) => writeFile(cssFile.replace('.css', '.min.css'), cssMin.css, { encoding: 'utf8', mode: 0o644 }))
    // eslint-disable-next-line no-console
    .then(() => console.log(`✅ SCSS File compiled: ${cssFile}`))
    .catch((error) => {
      // eslint-disable-next-line no-console
      console.error(error);
    });
};
