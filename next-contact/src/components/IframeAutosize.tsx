"use client";
import { useEffect } from "react";

export default function IframeAutosize() {
  useEffect(() => {
    const root = document.getElementById("wi-contact-root") || document.body;

    let last = 0;
    let raf = 0;

    const measure = () => Math.ceil(root.getBoundingClientRect().height);

    const post = () => {
      const h = measure();
      if (Math.abs(h - last) > 4) {
        last = h;
        window.parent?.postMessage(
          { type: "wi-iframe-height", height: h },
          "*"
        );
      }
    };

    // initial
    post();

    // observe layout changes
    const ro = new ResizeObserver(() => {
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(post);
    });
    ro.observe(root);

    // fallback ticks (fonts/images)
    const iv = window.setInterval(post, 1000);

    window.addEventListener("load", post);
    window.addEventListener("resize", post);

    return () => {
      ro.disconnect();
      clearInterval(iv);
      window.removeEventListener("load", post);
      window.removeEventListener("resize", post);
      cancelAnimationFrame(raf);
    };
  }, []);

  return null;
}
