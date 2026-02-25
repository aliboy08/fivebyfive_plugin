import { v4wp } from './v4wp/v4wp';
import mkcert from 'vite-plugin-mkcert';
import entrypoints from './vite-entrypoints.json';

export default {
	server: {
		https: true,
		cors: true,
	},
	plugins: [
		v4wp({
			input: entrypoints,
			outDir: 'dist',
		}),
		mkcert(),
	],
	resolve: {
		alias: {
			src: '/src',
			css: '/src/css',
			js: '/src/js',
		},
	},
};
