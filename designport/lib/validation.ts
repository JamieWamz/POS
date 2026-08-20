import { z } from "zod";

const safeUrl = z
  .string()
  .trim()
  .max(300)
  .refine(
    (value) => value.startsWith("#") || /^\/(?!\/)/.test(value) || /^https?:\/\//i.test(value),
    "Use a section link, relative path, or http(s) URL"
  );

const settingsSchema = z.object({
  align: z.enum(["left", "center"]),
  tone: z.enum(["plain", "accent", "contrast"])
});

const heroBlockSchema = z.object({
  id: z.string().min(1).max(100),
  type: z.literal("hero"),
  settings: settingsSchema,
  content: z.object({
    eyebrow: z.string().max(80),
    heading: z.string().min(1).max(140),
    body: z.string().max(400),
    ctaLabel: z.string().max(40),
    ctaUrl: safeUrl
  })
});

const textBlockSchema = z.object({
  id: z.string().min(1).max(100),
  type: z.literal("text"),
  settings: settingsSchema,
  content: z.object({
    heading: z.string().min(1).max(120),
    body: z.string().max(1200)
  })
});

const projectsBlockSchema = z.object({
  id: z.string().min(1).max(100),
  type: z.literal("projects"),
  settings: settingsSchema,
  content: z.object({
    heading: z.string().min(1).max(120),
    items: z
      .array(z.object({ title: z.string().min(1).max(100), description: z.string().max(300) }))
      .min(1)
      .max(12)
  })
});

const contactBlockSchema = z.object({
  id: z.string().min(1).max(100),
  type: z.literal("contact"),
  settings: settingsSchema,
  content: z.object({
    heading: z.string().min(1).max(120),
    body: z.string().max(400),
    email: z.string().email().max(254)
  })
});

export const portfolioBlockSchema = z.discriminatedUnion("type", [
  heroBlockSchema,
  textBlockSchema,
  projectsBlockSchema,
  contactBlockSchema
]);

export const portfolioCreateSchema = z.object({
  title: z.string().trim().min(1).max(80).default("Untitled portfolio")
});

export const portfolioUpdateSchema = z
  .object({
    title: z.string().trim().min(1).max(80).optional(),
    slug: z
      .string()
      .trim()
      .min(3)
      .max(48)
      .regex(/^[a-z0-9]+(?:-[a-z0-9]+)*$/, "Use lowercase letters, numbers and hyphens")
      .optional(),
    description: z.string().trim().max(180).optional(),
    blocks: z.array(portfolioBlockSchema).max(40).optional(),
    theme: z.enum(["atelier", "nocturne", "gallery"]).optional()
  })
  .refine((input) => Object.keys(input).length > 0, "At least one field is required");

export function getValidationMessage(error: z.ZodError) {
  return error.issues[0]?.message ?? "The submitted data is invalid.";
}
