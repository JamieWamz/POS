"use client";

import { usePathname } from "next/navigation";

export function NavVisibility({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  if (pathname.startsWith("/p/")) return null;
  return children;
}
