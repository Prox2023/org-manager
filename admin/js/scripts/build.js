const fs = require('fs');
const path = require('path');

// Path to build info file (two levels up from admin/js)
const buildInfoPath = path.join(__dirname, '../../../build.json');

// Read existing build info or create new
let buildInfo = {};
if (fs.existsSync(buildInfoPath)) {
    buildInfo = JSON.parse(fs.readFileSync(buildInfoPath));
}

// Update build number
buildInfo.build = Date.now();

// Write updated build info
fs.writeFileSync(buildInfoPath, JSON.stringify(buildInfo, null, 2));

console.log(`Build number updated to: ${buildInfo.build}`); 