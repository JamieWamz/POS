import { describe, expect, it } from "vitest";

import { portfolioUpdateSchema } from "@/lib/validation";

describe("portfolio update validation", () => {
  it("accepts safe metadata", () => {
    const result = portfolioUpdateSchema.safeParse({
      title: "A considered practice",
      slug: "a-considered-practice",
      description: "Selected product and brand work.",
      theme: "atelier"
    });
    expect(result.success).toBe(true);
  });

  it("rejects slugs that can escape the public route", () => {
    expect(portfolioUpdateSchema.safeParse({ slug: "../../admin" }).success).toBe(false);
    expect(portfolioUpdateSchema.safeParse({ slug: "Mixed Case" }).success).toBe(false);
  });

  it("rejects protocol-relative calls to action", () => {
    const hero = {
      id: "hero-test",
      type: "hero",
      settings: { align: "left", tone: "plain" },
      content: { eyebrow: "Designer", heading: "Hello", body: "Introduction", ctaLabel: "Go", ctaUrl: "//evil.example" }
    };
    expect(portfolioUpdateSchema.safeParse({ blocks: [hero] }).success).toBe(false);
  });

  it("rejects empty updates and unknown themes", () => {
    expect(portfolioUpdateSchema.safeParse({}).success).toBe(false);
    expect(portfolioUpdateSchema.safeParse({ theme: "neon" }).success).toBe(false);
  });
});
