#!/usr/bin/env sh
rm KnowledgeBaseApiModule-*.zip
zip -r KnowledgeBaseApiModule-2.0.0.zip KnowledgeBaseApiModule -x "*.DS_Store" -x ".git*" -x ".idea*" -x "*.gitkeep"
