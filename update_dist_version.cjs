const fs = require('node:fs');

update(process.argv[2]);

function update(v) {
	let data = fs.readFileSync('data.json', 'utf8');
	data = JSON.parse(data);
	data.dist_version = v;
	fs.writeFile(`data.json`, JSON.stringify(data), () => {});
}
