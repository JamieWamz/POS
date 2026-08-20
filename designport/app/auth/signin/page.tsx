import type { Metadata } from "next";
import { redirect } from "next/navigation";

import { OAuthButtons } from "@/components/ui/oauth-buttons";
import { auth } from "@/lib/auth";

export const metadata: Metadata = { title: "Sign in" };

export default async function SignInPage({ searchParams }: { searchParams: { error?: string } }) {
  const session = await auth();
  if (session) redirect("/dashboard");

  return (
    <main id="main-content" tabIndex={-1} className="grid min-h-[calc(100vh-4rem)] place-items-center px-4 py-16">
      <section className="glass-panel w-full max-w-md rounded-3xl p-6 sm:p-8" aria-labelledby="signin-title">
        <p className="eyebrow">Welcome to DesignPort</p>
        <h1 id="signin-title" className="mt-3 font-display text-3xl font-semibold">Make room for your best work.</h1>
        <p className="mt-3 text-sm leading-6 text-muted">Sign in securely to create, save and publish your portfolio.</p>
        {searchParams.error && (
          <div role="alert" className="mt-5 rounded-xl border border-red-400/40 bg-red-500/10 p-3 text-sm text-red-800 dark:text-red-200">
            Authentication was not completed. Please try again or choose another provider.
          </div>
        )}
        <div className="mt-8">
          <OAuthButtons
            googleEnabled={Boolean(process.env.GOOGLE_CLIENT_ID && process.env.GOOGLE_CLIENT_SECRET)}
            githubEnabled={Boolean(process.env.GITHUB_ID && process.env.GITHUB_SECRET)}
          />
        </div>
        <p className="mt-4 text-center text-xs leading-5 text-muted">By continuing, you agree to use DesignPort responsibly. We only request the profile details needed for your account.</p>
      </section>
    </main>
  );
}
