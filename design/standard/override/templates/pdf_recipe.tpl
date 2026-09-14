{* A recipe as it should appear in a printed book.

   The shipped node/view/pdf.tpl prints every attribute of the class in storage
   order with no labels. ng_recipe has twenty one of them and several are teaser
   copies of others, so every recipe came out with its headline twice, its
   photograph twice, its introduction twice, and then five bare numbers - 50,
   666, 3, 116, 33 - with nothing to say what any of them were.

   This picks the fields a reader wants and labels the ones that need it.
   Everything not named here is deliberately left out: the teaser copies, the
   tags, the metadata, css_class, url_text and the empty relation lists. *}

{def $nutrition=''}
{let map=$node.object.data_map}

{if $pdf_root_template|eq(1)}
  {pdf(pageNumber, hash( identifier, "main",
                         start, 1 ) )}
{/if}

{pdf(header, hash( level, 1,
                   text, $node.name|wash(pdf),
                   size, 20,
                   align, left ) )}

{* The photograph, once. image and teaser_image hold the same picture. *}
{if and( is_set( $map.image ), $map.image.has_content )}
  {attribute_pdf_gui attribute=$map.image}
  {pdf(newline)}
{/if}

{* The introduction, once. full_intro and teaser_intro hold the same words. *}
{if and( is_set( $map.full_intro ), $map.full_intro.has_content )}
  {attribute_pdf_gui attribute=$map.full_intro}
{/if}

{* Ingredients, preparation and the credits all live in the body. *}
{if and( is_set( $map.body ), $map.body.has_content )}
  {attribute_pdf_gui attribute=$map.body}
  {pdf(newline)}
{/if}

{* The numbers, on one line, each saying what it is. *}
{if and( is_set( $map.serving_calories ), $map.serving_calories.has_content )}
  {set $nutrition=concat( $nutrition, $map.serving_calories.content, ' kcal' )}
{/if}
{if and( is_set( $map.serving_fat ), $map.serving_fat.has_content )}
  {set $nutrition=concat( $nutrition, ' - ', $map.serving_fat.content, 'g fat' )}
{/if}
{if and( is_set( $map.serving_carbohydrates ), $map.serving_carbohydrates.has_content )}
  {set $nutrition=concat( $nutrition, ' - ', $map.serving_carbohydrates.content, 'g carbohydrate' )}
{/if}
{if and( is_set( $map.serving_protein ), $map.serving_protein.has_content )}
  {set $nutrition=concat( $nutrition, ' - ', $map.serving_protein.content, 'g protein' )}
{/if}
{if and( is_set( $map.preparation_time ), $map.preparation_time.has_content )}
  {set $nutrition=concat( $nutrition, ' - ', $map.preparation_time.content, ' minutes' )}
{/if}
{if $nutrition|ne('')}
  {* pdf(text) takes the words as its own parameter and the settings as the
     next one. Handed a hash it prints the word "Array". *}
  {pdf(text, concat( 'Per serving: ', $nutrition )|wash(pdf),
             hash( size, 9 ) )}
  {pdf(newline)}
{/if}

{* Anything filed below a recipe still follows it, as it did before. *}
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
{undef $nutrition}

{if $pdf_root_template|eq(1)}
  {pdf(pageNumber, hash( identifier, "main",
                         stop, 1 ) )}
  {include uri="design:content/pdf/footer.tpl"}
{/if}
