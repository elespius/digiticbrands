const Babel = require('./babel.js');
const Fs = require('fs');
const MakeDir = require('./make-dir.js');
const Path = require('path');

const headerText = `PLEASE DO NOT MODIFY THIS FILE. WORK ON THE ES6 VERSION.
OTHERWISE YOUR CHANGES WILL BE REPLACED ON THE NEXT BUILD.`;

// Predefine some settings
const settings = [
    {
        presets: [
            ['@babel/preset-env', {
                targets: {
                    browsers: ["ie 11"]
                },
                modules: false
            }],
        ],
        plugins: [
            ['add-header-comment', { header: [headerText] }],
            ['@babel/plugin-transform-classes']
        ],
        comments: true
    },
    {
    presets: [
        ['@babel/preset-env', {
            targets: {
                browsers: ["ie 11"]
            },
            modules: false
        }],
        "minify"
    ],
    plugins: [
        ['@babel/plugin-transform-classes']
    ],
    comments: false
}
];

/**
 * Compiles es6 files to es5.
 *
 * @param file the full path to the file + filename + extension
 */
const compileFile = (file) => {
  const filePath = file.slice(0, -7);

  const outputFiles = [
    `${filePath.replace('/build/media_src/', '/media/').replace('\\build\\media_src\\', '\\media\\')}.js`,
    `${filePath.replace('/build/media_src/', '/media/').replace('\\build\\media_src\\', '\\media\\')}.min.js`
  ];

  // Ensure that the directories exist or create them
  MakeDir.run(Path.dirname(file).replace('/build/media_src/', '/media/'));

  // Get the contents of the ES-XXXX file
  let es6File = Fs.readFileSync(file, 'utf8');

  settings.forEach((setting, index) => {
      Babel.run(es6File, setting, outputFiles[index])
  });
};

module.exports.compileFile = compileFile;
