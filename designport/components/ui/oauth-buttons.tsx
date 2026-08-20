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
        <Button className="w-full" onClick={() => authenticate("google")} disabled={pending !== null}>
          <span aria-hidden="true" className="text-base">G</span>
          {pending === "google" ? "Connecting…" : "Continue with Google"}
        </Button>
      )}
      {githubEnabled && (
        <Button variant="secondary" className="w-full" onClick={() => authenticate("github")} disabled={pending !== null}>
          <Github size={18} aria-hidden="true" />
          {pending === "github" ? "Connecting…" : "Continue with GitHub"}
        </Button>
      )}
      {!googleEnabled && !githubEnabled && (
        <p role="alert" className="rounded-xl border border-champagne-400/50 bg-champagne-400/10 p-3 text-sm leading-6 text-ink">
          No OAuth provider is configured. Add Google or GitHub credentials to the environment, then restart DesignPort.
        </p>
      )}
      <div aria-live="polite" className="min-h-5 text-sm text-red-700 dark:text-red-300">{error}</div>
    </div>
  );
}
