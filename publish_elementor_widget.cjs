const fs = require('node:fs');

copy_dist_files(process.argv[2]);

function copy_dist_files(slug) {
	const entry_point = get_entrypoint(slug);
	if (!entry_point) return;

	const dist_path = `dev/elementor_widgets/${slug}/dist`;

	if (fs.existsSync(dist_path)) {
		fs.rmSync(dist_path, { recursive: true, force: true });
	}

	fs.mkdirSync(dist_path);

	const copy_file = (path) => {
		const file_name = get_file_name(path);
		fs.copyFile(`dist/${path}`, `${dist_path}/${file_name}`, (err) => {
			if (err) throw err;
		});
	};

	copy_file(entry_point.file);

	entry_point.css.forEach((css_path) => {
		copy_file(css_path);
	});

	create_manifest(entry_point, dist_path);

	generate_custom_manifest('dist/wp-manifest.json', 'dist/.vite/manifest.json');
}

function get_entrypoint(slug) {
	let manifest = fs.readFileSync('dist/.vite/manifest.json', 'utf8');
	manifest = JSON.parse(manifest);
	for (const key in manifest) {
		const item = manifest[key];
		if (item['name'] === `elementor_widget_${slug}`) {
			return item;
		}
	}
}

function get_file_name(file_path) {
	const temp = file_path.split('/');
	return temp[temp.length - 1];
}

function create_manifest(entry_point, dist_path) {
	const manifest = {
		js: get_file_name(entry_point.file),
		css: [],
	};

	entry_point.css.forEach((css_path) => {
		manifest.css.push(get_file_name(css_path));
	});

	fs.writeFile(
		`${dist_path}/manifest.json`,
		JSON.stringify(manifest),
		() => {},
	);
}

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
