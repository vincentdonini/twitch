"use client"

import ReactMarkdown from "react-markdown"

interface MarkdownSectionProps {
  title?: string;
  content: string
}

export function MarkdownSection(
  {
    title,
    content,
  }: MarkdownSectionProps,
) {
  return (
    <div className="space-y-3">
      <h2 className="text-xl font-semibold">
        {title}
      </h2>
      <div className="prose prose-sm dark:prose-invert max-w-none">
        <ReactMarkdown>
          {content}
        </ReactMarkdown>
      </div>
    </div>
  )
}
