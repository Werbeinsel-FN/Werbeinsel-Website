import "leaflet/dist/leaflet.css";
import type { Metadata } from "next";
import "./globals.css";
import { Unbounded, Poppins } from "next/font/google";

const unbounded = Unbounded({
  subsets: ["latin", "latin-ext"],
  weight: ["400", "700", "800"],
  variable: "--font-unbounded",
  display: "swap",
});

const poppins = Poppins({
  subsets: ["latin", "latin-ext"],
  weight: ["400", "500", "700", "800"],
  variable: "--font-poppins",
  display: "swap",
});

export const metadata: Metadata = {
  title: "Kontakt | Werbeinsel",
  description: "Kontakt – Werbeinsel",
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="de">
      <body
        className={`${unbounded.variable} ${poppins.variable} antialiased bg-[#ffed00] text-black`}
      >
        {children}
      </body>
    </html>
  );
}
