
/**@type {import('next').NextConfig} */
const isProd = process.env.NODE_ENV === "production";
const nextConfig = {
  output: undefined,  // use output: 'export' for static pages. Uncomment the following line only for building purposes. By default, this line should remain commented out.
  reactStrictMode: true,
  trailingSlash: true,
  swcMinify: true,
  basePath: "",
	assetPrefix : "",
  // tesseract.js spawns a Node worker_thread pointing at its own on-disk
  // worker script, and playwright launches a browser binary via paths
  // relative to its package dir — webpack bundling these breaks both.
  // Keeping them external makes API routes that use them run as plain
  // Node requires instead.
  // tesseract.js spawns a Node worker_thread pointing at its own on-disk
  // worker script; webpack bundling it breaks that path. Keep it external
  // so the route that uses it runs as a plain Node require instead.
  experimental: {
    serverComponentsExternalPackages: ["tesseract.js", "cheerio", "playwright", "undici"],
  },
  eslint: {
    // eslint 9 removed the legacy config API that Next.js 14's lint step depends on;
    // linting is handled separately (npm run lint) rather than gating the build.
    ignoreDuringBuilds: true,
  },
  // images: {
  //   loader: "imgix",
  //   path: "/",
  // },
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "**", // Allows images from any domain
      },
    ],
  },
};

module.exports = nextConfig;
