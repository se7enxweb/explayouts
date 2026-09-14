<?php /* #?ini charset="utf-8"?

# Printed output for the content classes this solution ships.
#
# node/view/pdf.tpl, the template the pdf export renders each node with, prints
# every attribute of a class in storage order and labels none of them. For a
# class with teaser copies of its fields that means the headline, the
# photograph and the introduction all appear twice, and any numeric field
# appears as a bare number with nothing to say what it is.
#
# These two say what belongs in a printed page instead. They live in
# design/standard/override so they are found whichever design the export runs
# under, and the extension is already registered as a design extension.

[pdf_category]
Source=node/view/pdf.tpl
MatchFile=pdf_category.tpl
Subdir=templates
Match[class_identifier]=ng_category

[pdf_recipe]
Source=node/view/pdf.tpl
MatchFile=pdf_recipe.tpl
Subdir=templates
Match[class_identifier]=ng_recipe

*/ ?>
