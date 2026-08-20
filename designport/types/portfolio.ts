export const blockTypes = ["hero", "text", "projects", "contact"] as const;
export type BlockType = (typeof blockTypes)[number];

export type HeroContent = {
  eyebrow: string;
  heading: string;
  body: string;
  ctaLabel: string;
  ctaUrl: string;
};

export type TextContent = { heading: string; body: string };
export type ProjectsContent = {
  heading: string;
  items: Array<{ title: string; description: string }>;
};
export type ContactContent = { heading: string; body: string; email: string };

export type BlockContent = HeroContent | TextContent | ProjectsContent | ContactContent;

export type PortfolioBlock = {
  id: string;
  type: BlockType;
  content: BlockContent;
  settings: {
    align: "left" | "center";
    tone: "plain" | "accent" | "contrast";
  };
};

export type PortfolioDocument = {
  id: string;
  title: string;
  slug: string;
  description: string;
  blocks: PortfolioBlock[];
  theme: "atelier" | "nocturne" | "gallery";
  publishedAt: string | null;
  updatedAt: string;
};
