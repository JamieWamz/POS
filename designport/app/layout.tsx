import type { Metadata } from "next";
import { Cormorant_Garamond, Manrope } from "next/font/google";

import "@/app/globals.css";
import { Navbar } from "@/components/ui/navbar";
import { NavVisibility } from "@/components/ui/nav-visibility";
import { Providers } from "@/components/ui/providers";

const manrope = Manrope({ subsets: ["latin"], variable: "--font-manrope", display: "swap" });
const cormorant = Cormorant_Garamond({
  subsets: ["latin"],
  variable: "--font-cormorant",
  display: "swap",
  weight: ["500", "600", "700"]
});

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXTAUTH_URL ?? "http://localhost:3000"),
  title: { default: "DesignPort — Portfolios with presence", template: "%s · DesignPort" },
  description: "Build and publish a distinctive design portfolio without touching code.",
  openGraph: {
    title: "DesignPort",
    description: "Build and publish a distinctive design portfolio without touching code.",
    type: "website"
  }
};

const themeScript = `
  try {
    const stored = localStorage.getItem('designport-theme');
    const dark = stored === 'dark' || (!stored && matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
  } catch (_) {}
`;

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en" suppressHydrationWarning className={`${manrope.variable} ${cormorant.variable}`}>
      <head><script dangerouslySetInnerHTML={{ __html: themeScript }} /></head>
      <body>
        <a href="#main-content" className="fixed left-4 top-2 z-[100] -translate-y-20 rounded-lg bg-ink px-4 py-2 text-canvas focus:translate-y-0">
          Skip to main content
        </a>
        <Providers>
          <NavVisibility><Navbar /></NavVisibility>
          {children}
        </Providers>
      </body>
    </html>
  );
}
