// next.config.ts
import type { NextConfig } from "next";
import path from "path";

const base = process.env.NEXT_PUBLIC_BASE_PATH || "";

const nextConfig: NextConfig = {
  output: "export",
  basePath: base || undefined,
  assetPrefix: base || undefined,
  images: { unoptimized: true },
  outputFileTracingRoot: path.join(__dirname), // utišava warning
  // ako želiš da build ne puca na ESLint:
  // eslint: { ignoreDuringBuilds: true },
};

export default nextConfig;
