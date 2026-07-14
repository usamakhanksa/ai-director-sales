
/**@type {import('next').NextConfig} */
const isProd = process.env.NODE_ENV === "production";
const nextConfig = {
  output: undefined,  // use output: 'export' for static pages. Uncomment the following line only for building purposes. By default, this line should remain commented out.
  reactStrictMode: true,
  trailingSlash: true,
  swcMinify: true,
  basePath: "",
	assetPrefix : "",
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
