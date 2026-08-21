"use client";

import { Github } from "lucide-react";
import { signIn } from "next-auth/react";
import { useState } from "react";

import { Button } from "@/components/ui/button";

export function OAuthButtons({ googleEnabled, githubEnabled }: { googleEnabled: boolean; githubEnabled: boolean }) {
  const [pending, setPending] = useState<"google" | "github" | null>(null);
  const [error, setError] = useState("");

  async function authenticate(provider: "google" | "github") {
    try {
      setError("");
      setPending(provider);
      await signIn(provider, { callbackUrl: "/dashboard" });
    } catch {
      setError("Sign-in could not be started. Check your connection and try again.");
      setPending(null);
    }
  }

  return (
    <div className="space-y-3">
      {googleEnabled && (
        <Button className="w-full justify-between px-5" onClick={() => authenticate("google")} disabled={pending !== null}>
          <span aria-hidden="true" className="grid size-7 place-items-center rounded-full bg-white text-sm font-black text-neutral-900">G</span>
          {pending === "google" ? "Connecting…" : "Continue with Google"}
          <span aria-hidden="true">→</span>
        </Button>
      )}
      {githubEnabled && (
        <Button variant="secondary" className="w-full justify-between px-5" onClick={() => authenticate("github")} disabled={pending !== null}>
          <Github size={18} aria-hidden="true" />
          {pending === "github" ? "Connecting…" : "Continue with GitHub"}
          <span aria-hidden="true">→</span>
        </Button>
      )}
      {!googleEnabled && !githubEnabled && (
        <p role="alert" className="rounded-2xl border border-acid-500/40 bg-acid-400/[.15] p-4 text-sm leading-6 text-ink">
          <span className="mb-1 block font-extrabold">Studio access is not configured yet.</span>
          Add Google or GitHub OAuth credentials to the environment, then restart DesignPort.
        </p>
      )}
      <div aria-live="polite" className="min-h-5 text-sm text-red-700 dark:text-red-300">{error}</div>
    </div>
  );
}
