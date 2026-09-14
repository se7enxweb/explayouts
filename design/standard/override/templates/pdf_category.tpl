{* A category as a section opening in a printed book.

   ng_category carries its own settings alongside its words - show_children,
   fetch_subtree, page_limit, url_text - and the shipped template printed every
   one of them, so the section opening read "Recipes / Recipes / Yes / No / -1 /
   recipes" before it got to the sentence anybody wanted. Only the heading and
   the introduction are of use in print. *}

{let map=$node.object.data_map}

{if $pdf_root_template|eq(1)}
  {pdf(pageNumber, hash( identifier, "main",
                         start, 1 ) )}
{/if}

{pdf(header, hash( level, 1,
                   text, $node.name|wash(pdf),
                   size, 26,
                   align, left ) )}

{if and( is_set( $map.full_intro ), $map.full_intro.has_content )}
  {attribute_pdf_gui attribute=$map.full_intro}
  {pdf(newline)}
{/if}

{if $tree_traverse|eq(1)}
  {let children=fetch( content, list, hash( parent_node_id, $node.node_id,
                                            sort_by, $node.sort_array ) )}
    {section name=Child loop=$children}
      {if $class_array|contains( $Child:item.object.contentclass_id )}
        {node_view_gui view=pdf content_node=$Child:item tree_traverse=$tree_traverse class_array=$class_array}
      {/if}
    {/section}
  {/let}
{/if}

{/let}

{if $pdf_root_template|eq(1)}
  {pdf(pageNumber, hash( identifier, "main",
                         stop, 1 ) )}
  {include uri="design:content/pdf/footer.tpl"}
{/if}
