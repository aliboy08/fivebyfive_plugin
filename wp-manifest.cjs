const fs = require('node:fs');

// generate custom manifest file with only entry points
generate_custom_manifest('dist/wp-manifest.json', 'dist/.vite/manifest.json');

function generate_custom_manifest(output, input) {
	const mode = process.argv[2];

	if (mode === 'dev') {
		// create file as indicator for dev mode
		fs.writeFile('dist/mode.dev', '', () => {});
	} else {
		try {
			// build, remove dev file indicator
			fs.unlinkSync('dist/mode.dev');
		} catch (err) {}
	}

	const manifest_data = get_manifest_entry_points(input);

	fs.writeFile(output, JSON.stringify(manifest_data), () => {});
}

function get_manifest_entry_points(file_path) {
	const data = fs.readFileSync(file_path, 'utf8');
	const manifest_data = JSON.parse(data);

	const entry_points = {};
	for (const [key, value] of Object.entries(manifest_data)) {
		if (typeof value.isEntry === 'undefined' || !value.isEntry) continue;
		entry_points[value.name] = {
			file: value.file,
			css: value.css,
		};
	}

	return entry_points;
}
