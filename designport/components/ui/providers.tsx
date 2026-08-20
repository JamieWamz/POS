"use client";

import { SessionProvider } from "next-auth/react";
import { MotionConfig } from "framer-motion";

import { PageTransition } from "@/components/ui/page-transition";
import { ThemeProvider } from "@/components/ui/theme-provider";

export function Providers({ children }: { children: React.ReactNode }) {
  return (
    <SessionProvider>
      <MotionConfig reducedMotion="user">
        <ThemeProvider>
          <PageTransition>{children}</PageTransition>
        </ThemeProvider>
      </MotionConfig>
    </SessionProvider>
  );
}
