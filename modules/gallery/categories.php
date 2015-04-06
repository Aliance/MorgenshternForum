<?php
/*
+--------------------------------------------------------------------------
|   Invision Gallery Module v<#VERSION#>
|   ========================================
|   by Adam Kinder
|   (c) 2001 - 2005 Invision Power Services
|   ========================================
|   
|   Nullfied by SneakerXZ
|   
+---------------------------------------------------------------------------
*/


/**
* Main/Category Parser
*
* Reads the database for the nested categories and parsers
* the suitable output for general things such as the dropdown
* links
*
* @package		Gallery
* @subpackage 	Main
* @author   	Adam Kinder, Mark Wraith
* @version		<#VERSION#>
* @since 		1.0
*/

class Categories
{   
    var $ipsclass;
    var $glib;
    /* Current category */
    var $current;
    
    var $restrict = false;
    var $allowed_cats = 0;

    /**
     * Data read from DB 
     * 
     * [id] => array (
     *   'name'         => Name of category
     *   'parent'       => Parents id
     *   'child'        => An array of direct child category id's
     *   'descendants'  => An array of descendants - unlimited levels
     * )
     *
     */
    var $data;

    /* Bar styles for dropdown */
    var $full_bar = "|";
    var $half_bar = "'";    

    /* 
     * If set the value of the options are comma separated
     * with all the decendants added too
     */
    var $assign_sub_values = 0;

    /**
     * Reads and organises data into array
     * 
     * @param $check_permissions boolean Set to true to ignore private categories
     * @return true
     * @since 1.0
     * @access public
     *
     */
    
    function read_data ($check_permissions = true, $root='', $parent=0)
    {
        // Read the data from the database
        $this->ipsclass->DB->cache_add_query( 'get_all_categories', '', 'gallery_sql_queries' );									 
        $this->ipsclass->DB->simple_exec();  
        
        /**
        * Any allowed cats?
        **/
        $this->allowed_cats = $this->ipsclass->DB->get_num_rows();
        
        while ($row = $this->ipsclass->DB->fetch_row())
        {
            if( $check_permissions && ! $this->ipsclass->check_perms( $row['perms_thumbs'] ) )
            {
            	/**
            	* Skip
            	**/
            	$this->allowed_cats--;
            	continue;
            }

            // Add the information to the main array
            if( $this->restrict && !$row['album_mode'] )
            {
            	/**
            	* Skip
            	**/
            	$this->allowed_cats--;
            	continue;
            }
            if (!isset($this->data[ $row['id'] ])) 
            {
                $this->data[ $row['id'] ] = $row;
            }
            else 
            {
                $this->data[ $row['id'] ] = array_merge($this->data[ $row['id'] ],$row);                
            }

			if( $parent == $row['parent'] )
			{
				$this->ordered[] =& $this->data[ $row['id'] ];
			}

            // Let the parent category know they have a child
            $this->data[ $row['parent'] ]['child'][] = $row['id'];

            // Add our own id & our descendants to all the categories above
            if (isset($this->data[ $row['id'] ]['descendants']))
            {
                $to_add = array_merge($this->data[ $row['id'] ]['descendants'], array($row['id']));
            }
            else
            {
                $to_add = array($row['id']);
            }
            
            $this->_set_decendants($row['parent'],$to_add);
        }
		$this->data[0]['name'] = $root;
    }

    /**
     * Sets all the parents so they know their decendants id's in a single array
     * 
     * @param int $parent The parent of the category
     *        array $to_add The decendants to add to parents
     * @return true
     * @since 1.0
     * @access private
     *
     */
    
    function _set_decendants ($parent, $to_add)
    {
        $parent = $parent ? $parent : 0;
        
        if (isset($this->data[ $parent ]['descendants']))
        {
            $this->data[ $parent ]['descendants'] = array_merge($this->data[ $parent ]['descendants'],$to_add);
        }
        else
        {
            $this->data[ $parent ]['descendants'] = $to_add;
        }

        if (isset($this->data[ $parent ]['parent']))
        {
            // One up the tree
            $this->_set_decendants($this->data[ $parent ]['parent'], $to_add);
        }
    }
    

    
    /**
     * Build a category drop down
     * 
     * @param string $name The name of the select element created, if unset returned without a select tag
     * @return string The HTML built by the function
     * @since 1.0
     * @access public
     *
     */
    
    function build_dropdown ($name = false, $class="", $default=0)
    {
        // Start building the tree from the top branch

        $options = $this->_dropdown_branch($default);

        // Return output
        if ($name)
        {
        	$return = '<select name="'.$name . '"';
        	$return .= ( empty( $class ) ) ? '>' : ' class="' . $class . '">';
        	$return .= $options.'</select>';
            return $return;
        }
        else
        {
            return $options;
        }        
    }

    
    /**
     * Builds a particular branch of the category dropdown
     * 
     * @param int $category_id The id of the parent category 
     *        array $indent_array The past styles of indentation
     * @return string
     * @since 1.0
     * @access private
     *
     */
    
    function _dropdown_branch ( $category_id, $indent_array = false)
    {
        // Quick reference
        $category =& $this->data[ $category_id ];

        $output = '';
        
        // General indentation
        if (count($indent_array) > 1)
        {
            $indent = implode('&nbsp;&nbsp;&nbsp;&nbsp;',$indent_array);
            $indent .= '-&nbsp;';
        }
        else
        {
            $indent = '';
        }

        // Set the value to just the id or all sub id's too?      
        if ($this->assign_sub_values && isset($category['descendants']))
        {
            $value = implode(',',array_merge($category['descendants'],array($category_id)));
        }
        else
        {
            $value = $category_id;
        }

        // Should we select it as default or not?
        if ($this->current == $category_id)
        {
            $selected = ' selected="selected"';
        }
        else
        {
            $selected = '';
        }
        
        // Create html for option           
        $output .= '<option value="'.$value.'"'.$selected.'>'.$indent.$category['name'].'</option>';

        // Build further sub-categories
        if (isset($category['child']))
        {
            $i = 0;
            
            foreach ($category['child'] as $child)
            {               
                $i++;
                
                if ($category_id || $this->base->var['category_root_indent'])
                {
                    $new_indent_array = (array)$indent_array;
                    $last = array_pop($new_indent_array);

                    // If the parent was the last element swap the bar with a space
                    // otherwise leave it be
                    if ($last == $this->half_bar)
                    {
                        $new_indent_array[] = "&nbsp;";
                    }
                    else
                    {
                        $new_indent_array[] = $last;
                    }

                    // Is this the last element under this category, if so do a half
                    // bar not a full one
                    if ($i == count($category['child']))
                    {
                        $new_indent_array[] = $this->half_bar;
                    }
                    else
                    {
                        $new_indent_array[] = $this->full_bar;
                    }
                }
                else
                {
                    // We are at the root so just define it
                    $new_indent_array = array();
                }
                
                // Call the sub-branch parser and add to output
                $output .= $this->_dropdown_branch($child, $new_indent_array);
            }
        }

        // Return HTML
        return $output;
    }

    function _build_category_breadcrumbs( $current )
    {
        $this->_build_category_breadcrumbs_helper( $current );       

        if( is_array( $this->arr ) )
        {
            return( array_reverse( $this->arr ) );
        }
    }

    function _build_category_breadcrumbs_helper( $current )
    {
        $data = $this->data[$current];
        $cat = ( empty( $data['id'] ) ) ? '&op=user' : "&cat={$data['id']}";
        $data['name'] = ( empty( $data['id'] ) ) ? 'Members Gallery' : $data['name'];
        	
        $this->arr[] = "<a href='{$this->ipsclass->base_url}automodule=gallery&cmd=sc{$cat}'>{$data['name']}</a>";

        if( $this->data[$current]['parent'] )
        {
            $this->_build_category_breadcrumbs_helper( $this->data[$current]['parent'] );
        }
        
        return $current;
    }

}

?>
