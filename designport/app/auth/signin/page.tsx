import type { Metadata } from "next";
import { Check, Layers3, Sparkles } from "lucide-react";
import { redirect } from "next/navigation";

import { OAuthButtons } from "@/components/ui/oauth-buttons";
import { auth } from "@/lib/auth";

export const metadata: Metadata = { title: "Sign in" };

export default async function SignInPage({ searchParams }: { searchParams: { error?: string } }) {
  const session = await auth();
  if (session) redirect("/dashboard");

  return (
    <main id="main-content" tabIndex={-1} className="min-h-[calc(100vh-4.5rem)] p-3 sm:p-5 lg:p-6">
      <div className="mx-auto grid min-h-[calc(100vh-6rem)] max-w-[1536px] overflow-hidden rounded-[2rem] border bg-surface shadow-luxury lg:grid-cols-[1.05fr_.95fr]">
        <section className="relative hidden overflow-hidden bg-ink p-12 text-white lg:flex lg:flex-col xl:p-16" aria-labelledby="signin-promise">
          <div aria-hidden="true" className="absolute inset-0 opacity-25 [background-image:linear-gradient(rgba(255,255,255,.16)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.16)_1px,transparent_1px)] [background-size:64px_64px]" />
          <div aria-hidden="true" className="absolute -bottom-32 -right-24 size-[30rem] rounded-full bg-signal-500/[.45] blur-[110px]" />
          <div className="relative flex items-center justify-between">
            <span className="signal-chip">Private studio</span>
            <span className="font-mono text-[0.6875rem] uppercase tracking-[.18em] text-white/[.45]">Secure OAuth / JWT</span>
          </div>
          <div className="relative my-auto max-w-2xl py-16">
            <Sparkles className="mb-7 text-acid-400" size={34} aria-hidden="true" />
            <h1 id="signin-promise" className="font-display text-6xl font-semibold leading-[.9] tracking-[-0.05em] xl:text-7xl">A focused room for <span className="italic text-acid-400">your best work.</span></h1>
            <p className="mt-7 max-w-lg text-base leading-8 text-white/[.65]">Return to your canvas, shape the narrative, and publish when every detail feels like you.</p>
          </div>
          <ul className="relative grid gap-3 border-t border-white/[.15] pt-7 text-xs font-bold text-white/60 sm:grid-cols-3">
            {["Autosaved drafts", "Accessible output", "One-click publishing"].map((item) => <li key={item} className="flex items-center gap-2"><Check size={14} className="text-acid-400" aria-hidden="true" />{item}</li>)}
          </ul>
        </section>

        <section className="grid place-items-center px-5 py-14 sm:px-10 lg:px-14" aria-labelledby="signin-title">
          <div className="w-full max-w-md">
            <span className="grid size-12 place-items-center rounded-2xl bg-signal-500/10 text-signal-700 dark:text-signal-400"><Layers3 size={22} aria-hidden="true" /></span>
            <p className="eyebrow mt-9">Welcome back / 01</p>
            <h2 id="signin-title" className="mt-4 font-display text-4xl font-semibold leading-none tracking-[-0.035em] sm:text-5xl">Enter your studio.</h2>
            <p className="mt-4 text-sm leading-7 text-muted">Sign in securely to create, save, and publish your portfolio.</p>
            {searchParams.error && (
              <div role="alert" className="mt-6 rounded-2xl border border-red-400/40 bg-red-500/10 p-4 text-sm text-red-800 dark:text-red-200">
                Authentication was not completed. Please try again or choose another provider.
              </div>
            )}
            <div className="mt-9"><OAuthButtons googleEnabled={Boolean(process.env.GOOGLE_CLIENT_ID && process.env.GOOGLE_CLIENT_SECRET)} githubEnabled={Boolean(process.env.GITHUB_ID && process.env.GITHUB_SECRET)} /></div>
            <p className="mt-6 border-t pt-5 text-xs leading-5 text-muted">We only request the profile details required for your account. Your portfolio remains private until you explicitly publish it.</p>
          </div>
        </section>
      </div>
    </main>
  );
}
