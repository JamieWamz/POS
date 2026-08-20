import { describe, expect, it } from "vitest";

import { blockCatalog, createBlock, starterBlocks } from "@/lib/blocks";
import { portfolioBlockSchema } from "@/lib/validation";

describe("portfolio block templates", () => {
  it("creates a valid template for every catalog entry", () => {
    for (const entry of blockCatalog) {
      expect(portfolioBlockSchema.safeParse(createBlock(entry.type)).success).toBe(true);
    }
  });

  it("uses stable, unique starter block ids", () => {
    const blocks = starterBlocks();
    expect(new Set(blocks.map((block) => block.id)).size).toBe(blocks.length);
    expect(blocks).toHaveLength(3);
  });
});
