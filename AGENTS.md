# Agent Instructions

This repository is an opinionated WordPress starter used for real client sites.

`README.md` is the primary reference for how the theme is structured and how its existing systems are intended to work. Before modifying a documented system, read the relevant README section.

## Working Style

- Unless asked for a plan, proceed with the requested work without waiting for approval.
- Prefer the smallest change that fully solves the request.
- Before adding new code, inspect the existing implementation for helpers, utilities, component classes, patterns, and documented configuration that already solve the problem.
- If the requested result can be achieved by enabling, adjusting, or reusing an existing documented system, prefer that over rebuilding the feature.
- Reuse or modify existing systems where appropriate instead of duplicating or rebuilding them.
- Do not redesign, generalize, or refactor adjacent systems unless the task requires it.
- Do not edit WordPress core, third-party plugins, vendor code, generated dependencies, or other files that are not owned by this project.
- Do not edit compiled assets in `assets/public/` directly.

## Scope and Cleanup

If code is already being modified as part of the requested work, clean up directly adjacent redundancy or obvious problems when doing so improves the implementation.

If you discover unrelated technical debt or questionable code that does not need to be touched for the current task, leave it unchanged and mention it at the end of your response.

Avoid unnecessary duplication. Do not copy large blocks of existing code just to introduce a small variation when the existing implementation can reasonably be extended.

Before creating a new helper, utility, component class, field pattern, or template convention, search the codebase for an existing equivalent.

## README and Existing Architecture

Treat documented architecture as intentional.

Before changing systems such as navigation, footers, typography, buttons, layout helpers, ACF structures, blog components, permissions, or other documented features:

1. Find the relevant section in `README.md`.
2. Check for existing helpers, classes, flags, contracts, or troubleshooting guidance.
3. Preserve those contracts unless the requested work explicitly requires changing them.

Do not make one layer compensate for a problem that belongs to another layer.

## Frontend

- Use TypeScript for new frontend theme code rather than plain JavaScript unless an existing integration specifically requires JavaScript.
- Prefer Tailwind utilities for normal layout, spacing, responsive behavior, and one-off presentation where they fit naturally.
- Prefer CSS Grid / Tailwind grid for page and section layout where appropriate.
- Use dedicated CSS for reusable components, complex selectors/states, or systems that are already implemented as CSS components.
- Do not convert an existing CSS component to Tailwind solely for consistency.
- Use existing theme tokens and component classes instead of recreating equivalent styles.
- Avoid magic values when an existing token, helper, utility, or component already represents the intended design.
- Buttons and other established components should use the existing component system unless the task specifically calls for something new.
- Preserve semantic markup, heading hierarchy, ARIA attributes, focus behavior, alt-text behavior, and other existing accessibility features.
- Responsive behavior is part of the implementation. When only a desktop mockup is provided, infer sensible tablet and mobile behavior from the existing design system and nearby components.

## ACF Flexible Content Workflow

When asked to build a new ACF Flexible Content section from a mockup or design reference, use this default workflow unless the user gives more specific instructions:

1. Determine the minimum ACF fields needed to reproduce the design and make the section appropriately reusable, unless the user has already specified the fields.
2. Create or update the ACF field structure using the project's existing conventions.
3. Build the frontend section to match the provided mockup as closely as practical on the first pass.
4. Review the finished implementation against the reference and correct obvious mismatches before reporting completion.

Do not stop after creating a rough functional version.

Aim for approximately 90% visual fidelity on the first pass. Prioritize:

- overall layout
- proportions
- spacing
- alignment
- typography scale and hierarchy
- colors and backgrounds
- image sizing and cropping
- responsive behavior

If a mockup is provided, do not interpret “build this section” as “create a rough functional version.” Visual fidelity is part of the task.

Before declaring visual work complete, perform a final self-review against the provided reference and fix obvious mismatches.

Use existing helpers, tokens, components, and documented patterns before introducing new ones.

Do not hardcode client-facing content that should reasonably be editable through ACF unless the user explicitly requests a static implementation.

## WordPress and Database Work

WordPress/database changes are allowed when they are part of the requested task.

- Prefer WP-CLI or existing project tooling where practical.
- Creating or editing pages, menus, options, ACF data, and similar WordPress content is allowed when requested.
- Prefer moving content to Trash over permanent deletion when WordPress supports it.
- Do not perform unrelated destructive or bulk database operations.
- Do not modify WordPress core or third-party plugin code.
- Mention important database/content changes in the completion summary because they may not appear in the Git diff.

## Dependencies and Updates

Do not install, remove, or update dependencies without explicit approval for the current task.

This includes, but is not limited to:

- `yarn add`
- package upgrades
- `composer require`
- Composer updates
- WordPress plugin installation
- WordPress plugin updates
- WordPress core updates

If a new dependency would materially improve the solution, explain why and ask first.

If the user explicitly approves an installation or update for the current task, proceed only within that scope.

## Documentation

If a change would alter the reusable architecture, conventions, setup process, or behavior documented in `README.md`, ask before updating the documentation.

Some changes are intentionally project-specific and should not become permanent starter-theme conventions.

## Content

If exact copy has not been provided, do not invent production copy.

Use clearly recognizable placeholder content such as:

- Lorem ipsum
- `John LastName`
- `Company Name`
- `Example Heading`

Do not present generated marketing copy as final client copy unless explicitly asked to write it.

## Verification

During normal development, do not run every available check after every edit.

Before creating a requested Git commit:

1. Run `yarn dev`.
2. Confirm it completes without errors.
3. Run additional linting, formatting, typechecking, or other checks only when relevant to the changed code or explicitly requested.

The developer may already have `yarn dev:watch` running. Do not start another watch process and do not terminate existing developer processes without permission.

If an active watch process prevents `yarn dev` from being run safely, report that rather than stopping it automatically.

## Git

Do not commit automatically.

Only commit after the developer has reviewed/approved the work in chat and explicitly asks for a commit.

- Group related changes into one commit.
- Split unrelated work into separate commits.
- Never push unless explicitly asked.
- Never amend unless explicitly asked.
- Never use `--no-verify` unless explicitly asked.
- Never commit secrets, `.env` files, credentials, API keys, or other sensitive configuration.

### Commit Message Format

Use:

1. One approved gitmoji shortcode.
2. A space.
3. One imperative subject line, maximum 50 characters.
4. A blank line.
5. Two or three concise sentences explaining what changed and why.

Use a HEREDOC when creating the commit message.

Choose exactly one:

- `:white_check_mark:` Adding a new file
- `:x:` Removing a file
- `:repeat:` Refactoring or simplifying structure
- `:rocket:` Completing a section, feature, or page
- `:art:` CSS, Tailwind, or cosmetic changes
- `:bug:` Fixing a bug or issue
- `:fire:` Deleting a section of code
- `:scroll:` Documentation or README changes
- `:green_heart:` Improving or writing comments
- `:shirt:` Removing linter warnings
- `:sparkles:` Converting static content to dynamic content
- `:arrow_up:` Updating dependencies
- `:zap:` Improving performance or experience
- `:lock:` Security-related changes
- `:twisted_rightwards_arrows:` Merging branches
- `:rewind:` Reverting changes

If none apply, use `??`.

## Completion Notes

When finished:

- Briefly summarize what changed.
- Mention any relevant verification performed.
- Mention unrelated problems you noticed but deliberately did not modify.
- Call out database/content changes that will not appear in the Git diff.
