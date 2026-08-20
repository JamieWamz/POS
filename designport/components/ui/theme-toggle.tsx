"use client";

import { Moon, Sun } from "lucide-react";

import { Button } from "@/components/ui/button";
import { useTheme } from "@/components/ui/theme-provider";

export function ThemeToggle() {
  const { theme, toggleTheme, mounted } = useTheme();
  const nextTheme = theme === "dark" ? "light" : "dark";

  return (
    <Button
      variant="ghost"
      onClick={toggleTheme}
      className="size-11 px-0"
      aria-label={mounted ? `Switch to ${nextTheme} mode` : "Toggle color theme"}
      title={mounted ? `Switch to ${nextTheme} mode` : "Toggle color theme"}
    >
      {mounted && theme === "dark" ? <Sun aria-hidden="true" size={19} /> : <Moon aria-hidden="true" size={19} />}
    </Button>
  );
}
