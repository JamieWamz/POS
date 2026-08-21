import Link from "next/link";

import { Logo } from "@/components/ui/logo";
import { SignOutButton } from "@/components/ui/sign-out-button";
import { ThemeToggle } from "@/components/ui/theme-toggle";
import { auth } from "@/lib/auth";
import { buttonVariants } from "@/components/ui/button-variants";

export async function Navbar() {
  const session = await auth();

  return (
    <header className="sticky top-0 z-50 border-b border-line/80 bg-canvas/88 backdrop-blur-2xl">
      <nav className="mx-auto flex h-[4.5rem] max-w-[1536px] items-center justify-between px-4 sm:px-6 lg:px-8" aria-label="Primary navigation">
        <Logo />
        <div className="hidden items-center gap-7 md:flex" aria-label="Product links">
          <Link href="/#features" className="text-xs font-bold text-muted transition-colors hover:text-ink">Features</Link>
          <Link href="/#process" className="text-xs font-bold text-muted transition-colors hover:text-ink">How it works</Link>
          <span className="signal-chip"><span className="size-1.5 rounded-full bg-neutral-950" aria-hidden="true" /> Free to start</span>
        </div>
        <div className="flex items-center gap-1 sm:gap-2">
          <ThemeToggle />
          {session?.user ? (
            <>
              <Link href="/dashboard" className={buttonVariants("secondary")}>Dashboard</Link>
              <SignOutButton />
            </>
          ) : (
            <Link href="/auth/signin" className={buttonVariants("primary")}>Start building</Link>
          )}
        </div>
      </nav>
    </header>
  );
}
