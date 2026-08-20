"use client";

import * as React from "react";
import { motion } from "framer-motion";

import { buttonVariants, type ButtonVariant } from "@/components/ui/button-variants";
import { cn } from "@/lib/utils";

type ButtonProps = React.ComponentPropsWithoutRef<typeof motion.button> & {
  variant?: ButtonVariant;
};

export const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = "primary", type = "button", children, ...props }, ref) => (
    <motion.button
      ref={ref}
      type={type}
      whileHover={{ y: -1 }}
      whileTap={{ scale: 0.98 }}
      className={cn(buttonVariants(variant), className)}
      {...props}
    >
      {children}
    </motion.button>
  )
);

Button.displayName = "Button";
