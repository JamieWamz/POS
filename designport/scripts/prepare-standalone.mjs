import { cp, mkdir, stat } from "node:fs/promises";
import path from "node:path";

const root = process.cwd();
const standaloneRoot = path.join(root, ".next", "standalone");

async function copyRequired(source, target) {
  await stat(source);
  await mkdir(path.dirname(target), { recursive: true });
  await cp(source, target, { recursive: true, force: true });
}

async function copyOptional(source, target) {
  try {
    await stat(source);
  } catch (error) {
    if (error && typeof error === "object" && "code" in error && error.code === "ENOENT") return;
    throw error;
  }
  await mkdir(path.dirname(target), { recursive: true });
  await cp(source, target, { recursive: true, force: true });
}

try {
  await copyRequired(
    path.join(root, ".next", "static"),
    path.join(standaloneRoot, ".next", "static")
  );
  await copyOptional(path.join(root, "public"), path.join(standaloneRoot, "public"));
  console.log("Standalone assets packaged successfully.");
} catch (error) {
  console.error("Failed to package standalone assets.", error);
  process.exitCode = 1;
}
