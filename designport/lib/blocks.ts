import type { BlockType, PortfolioBlock } from "@/types/portfolio";

export const blockCatalog: Array<{
  type: BlockType;
  label: string;
  description: string;
}> = [
  { type: "hero", label: "Hero", description: "Introduce your work with one strong statement." },
  { type: "text", label: "Story", description: "Add a concise biography or process note." },
  { type: "projects", label: "Projects", description: "Show a focused collection of selected work." },
  { type: "contact", label: "Contact", description: "Give visitors one clear way to reach you." }
];

export function createBlock(type: BlockType, id = crypto.randomUUID()): PortfolioBlock {
  const settings: PortfolioBlock["settings"] = { align: "left", tone: "plain" };

  switch (type) {
    case "hero":
      return {
        id,
        type,
        settings,
        content: {
          eyebrow: "Independent designer",
          heading: "I shape thoughtful digital experiences.",
          body: "Strategy, identity and product design for teams building what matters next.",
          ctaLabel: "Explore my work",
          ctaUrl: "#projects"
        }
      };
    case "text":
      return {
        id,
        type,
        settings,
        content: {
          heading: "A little about me",
          body: "I pair careful research with expressive visual systems to make complex products feel beautifully simple."
        }
      };
    case "projects":
      return {
        id,
        type,
        settings,
        content: {
          heading: "Selected work",
          items: [
            { title: "Northstar", description: "A calmer way for distributed teams to plan." },
            { title: "Canopy", description: "A climate platform designed for decisive action." }
          ]
        }
      };
    case "contact":
      return {
        id,
        type,
        settings,
        content: {
          heading: "Let’s make something memorable.",
          body: "Have an ambitious idea? I would love to hear where you want to take it.",
          email: "hello@example.com"
        }
      };
  }
}

export function starterBlocks() {
  return [
    createBlock("hero", "starter-hero"),
    createBlock("projects", "starter-projects"),
    createBlock("contact", "starter-contact")
  ];
}
