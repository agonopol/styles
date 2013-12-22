<?php
/**********************************************************************
* OSAKA UNLIMITED WORDPRESS THEME 
* (Ideal For Business And Personal Use: Portfolio or Blog)     
* 
* File name:   
*      cp_renderer.php
* Brief:       
*      Part of theme control panel.
* Author:      
*      DigitalCavalry
* Author URI:
*      http://themeforest.net/user/DigitalCavalry
* Contact:
*      digitalcavalry@gmail.com 
***********************************************************************/

/*********************************************************** 
* Class name:
*    DCC_ControlPanelRenderer
* Descripton:
*    Implementation of DCC_ControlPanelRenderer 
***********************************************************/
class DCC_ControlPanelRenderer extends DCC_BasicTools
{

    /*********************************************************** 
    * Constructor
    ************************************************************/
    public function __construct() 
    {
    
    } // constructor 

    /*********************************************************** 
    * Public members
    ************************************************************/      
    
    /*********************************************************** 
    * Private members
    ************************************************************/      
   
    /*********************************************************** 
    * Public functions
    ************************************************************/                
    public function wpBreadcrumb($args=array(), $echo=true)
    {
        global $dc_page_common_opt;
        global $post;
        
        $def = array(
            'id' => null,
            'level' => 0,
            'title' => null,
            'before' => null, // array of name value pairs for additional links
            'empty' => false
        );
        
        $args = $this->combineArgs($def, $args);
        $out = '';
        
        $hide_on_page = false;
        if($dc_page_common_opt !== false and $post->post_type == 'page' and $dc_page_common_opt['page_misc_hide_breadcrumb_cbox'])
        {
            $hide_on_page = true;    
        }
        
        if(!(bool)GetDCCPI()->getIGeneral()->getOption('breadcrumb_display') or $args['empty'] or $hide_on_page) 
        { 
            $out .= '<div class="dc-wp-breadcrumb-navigation-empty"></div>';
            if($echo) { echo $out; return; } else { return $out; } 
        } 
            
        if($args['level'] == 0)
        {
            $out .= '<div class="dc-sixteen dc-columns">';
            $out .= '<div class="dc-wp-breadcrumb-navigation">';
        }
        if($args['id'] === null)
        {
            global $post;
            $args['id'] = $post->ID;    
        }
        
        $p = null;
        $parent = 0;
        if($args['id'] !== null)
        {    
            global $wpdb;
            $id = $args['id'];
            $p = $wpdb->get_row("SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE ID = $id");             
            $parent = $p->post_parent;
        }
        
        if($parent != 0)
        {
            $level = $arg['level']+1;
            $out .= $this->wpBreadcrumb(array('id' => $parent, 'level' => $level), false);    
        } else
        {        
             if(GetDCCPI()->getIGeneral()->getOption('breadcrumb_before_display'))
             {
                $out .= '<span class="before">'; 
                $out .= GetDCCPI()->getIGeneral()->getOption('breadcrumb_before').' ';
                $out .= '</span>';                  
             }   
             
             if(GetDCCPI()->getIGeneral()->getOption('breadcrumb_blog_name_display'))
             {
                $blogname = get_bloginfo('name');
                if(GetDCCPI()->getIGeneral()->getOption('breadcrumb_blog_name_force'))
                {
                    $blogname = GetDCCPI()->getIGeneral()->getOption('breadcrumb_blog_name');   
                }                                
                $out .= '<a class="link" href="'.get_bloginfo('url').'">'.$blogname.'</a>';
                $out .= $this->getBreadcrumbSeprator();
             }
        }  
        
        if($args['level'] == 0)
        {
            if($args['before'] !== null)
            {
                if(is_array($args['before']))
                {
                    $out .= $this->getBreadcrumbSeprator();
                    $counter = 0;
                    foreach($args['before'] as $key => $value)
                    {
                        if($counter > 0) { echo ', '; }
                        $out .= '<a href="'.$value.'" class="link">'.$key.'</a>';
                        $counter++;    
                    }    
                }
            }        
            
            if($args['title'] === null)
            {
                if($parent != 0) { $out .= $this->getBreadcrumbSeprator(); }
                $out .= '<sapn class="selected">'.$p->post_title.'</span>';
            } else
            {   
                if($parent != 0) { $out .= $this->getBreadcrumbSeprator(); }
                $out .= '<span class="selected">'.$args['title'].'</span>';
            }
            
                $out .= '</div>';
            $out .= '</div>'; 
        } else
        {
            if($parent != 0) { $out .= $this->getBreadcrumbSeprator(); }
            $out .= '<a href="'.get_permalink($p->ID).'" class="link">'.$p->post_title.'</a>';
        }    
        
        if($echo) { echo $out; } else { return $out; }          
    }
    
    private function getBreadcrumbSeprator($echo=false)
    {
        $out = '<span class="separator">/</span>';
        
        if($echo) { echo $out; } else { return $out; }
    }   
    
    public function getTopEmptySpace($echo=false)
    {
        $out = '';
        $h = (int)GetDCCPI()->getIGeneral()->getOption('theme_top_empty_space');
        
        if($h > 0)
        {
            $out .= '<div class="dc-theme-top-empty-space" style="height:'.$h.'px" ></div>';
        }
        
        if($echo) { echo $out; } else { return $out; }
    }

    public function getBottomEmptySpace($echo=false)
    {
        $out = '';
        $h = (int)GetDCCPI()->getIGeneral()->getOption('theme_bottom_empty_space');
        
        if($h > 0)
        {
            $out = '<div class="dc-theme-bottom-empty-space" style="height:'.$h.'px" ></div>';
        }
        
        if($echo) { echo $out; } else { return $out; }
    }
    
    public function wpCommentsBlock()
    {
        global $post;
        
        if(($post->post_type == 'page' and GetDCCPI()->getIGeneral()->getOption('comments_in_pages_display')) or
           ($post->post_type == 'post' and GetDCCPI()->getIGeneral()->getOption('comments_in_posts_display')) or 
           ($post->post_type == DCC_ControlPanelCustomPosts::PT_PROJECT_POST))
        {    
            if('open' == $post->comment_status)
            {            
                comments_template();
            }         
        }        
    }     
 

    public function wpPaginationBlock($echo=true)
    {   
        $SHOW_BEFORE = false;
        $before = '';
        if($SHOW_BEFORE) { $before = '<span class="before">'.__('Pages', CMS_TXT_DOMAIN).': </span>'; }
        
        $args = array(                                                                            
            'before'           => '<div class="dc-wp-multipage-pages-links">'.$before,
            'after'            => '<div class="dc-clear-both"></div></div>',
            'link_before'      => '<span>',
            'link_after'       => '</span>',
            'next_or_number'   => 'number',
            'nextpagelink'     => __('Next page', CMS_TXT_DOMAIN),
            'previouspagelink' => __('Previous page', CMS_TXT_DOMAIN),
            'pagelink'         => '%',
            'more_file'        => '',
            'echo'             => false ); 
        
        $out = '';    
        $out = wp_link_pages($args); 
        
        if($echo) { echo $out; } else { return $out; }                       
    }    
    
    public function wpQueryPaginationBlock($args=array(), $echo=false)
    {      
        $def = array(
            'paged' => 1,
            'maxpage' => 1,
            'all' => false,
            'pb' => 10,         # padding bottom
            'top' => 25         # margin top
        );
        $args = $this->combineArgs($def, $args);
        
        $SHOW_BEFORE_TEXT = false;
        $BEFORE_DOTS = 4;        
        $SHOW_PREV_NEXT_BTN = false;        
        $out = '';
        
        $w_style = '';
            $w_style .= 'margin-top:'.$args['top'].'px;';
            $w_style .= 'padding-bottom:'.$args['pb'].'px;';
        $w_style = ' style="'.$w_style.'" ';        
        
        if($args['all'])
        {
            if($args['maxpage'] > 1)
            {
                
                $out .= '<div class="dc-wp-query-pages-links" '.$w_style.'>';
                if($SHOW_BEFORE_TEXT)
                {
                    $out .= '<span class="before">'.__('Pages', CMS_TXT_DOMAIN).':</span>';
                }
            
                for($i = 1; $i <= $args['maxpage']; $i++)
                {
                    if($i == $args['paged'])
                    {
                        $out .= '<a class="current-page" >'.$i.'</a>';    
                    } else
                    {
                        $out .=  '<a href="'.get_pagenum_link($i).'">'.$i.'</a>';
                    }
                } 
            
                $out .= '<div class="dc-clear-both"></div></div>';  
            }                        
        } else
        {        
            if($args['maxpage'] > 1) 
            { 
                $out .= '<div class="dc-wp-query-pages-links" '.$w_style.'>';
                if($SHOW_BEFORE_TEXT)
                {
                    $out .= '<span class="before">'.__('Pages', CMS_TXT_DOMAIN).':</span>';
                }
                
                if($args['paged'] > 1 and $SHOW_PREV_NEXT_BTN)
                {
                    $out .= '<a class="prev-btn" href="'.get_pagenum_link($args['paged']-1).'">'.__('Prev', CMS_TXT_DOMAIN).'</a>'; 
                }             
                            
                if($args['maxpage'] > 15)
                {
                     $start = $args['paged'] - $BEFORE_DOTS;
                     if($start < 1) { $start = 1; }
                     $last_end = 0;
                     if($start > 5)
                     {
                      
                        $last_end = 2;
                        for($i = 1; $i <= $last_end; $i++)
                        {
                            if($i == $args['paged'])
                            {
                                $out .= '<a class="current-page" >'.$i.'</a>';    
                            } else
                            {
                                $out .= '<a href="'.get_pagenum_link($i).'">'.$i.'</a>';
                            }
                        }                    
                        $out .= '<span class="separator">...</span>';    
                     }
                                     

                    $start = $args['paged'] - $BEFORE_DOTS;
                    if($start < 6) { $start = 1; }
                    $last_end = $args['paged']+$BEFORE_DOTS;
                    if($last_end > $args['maxpage'])
                    {
                        $last_end = $args['maxpage'];
                    }
                    for($i = $start; $i <= $last_end; $i++)
                    {
                        if($i == $args['paged'])
                        {
                            $out .= '<a class="current-page" >'.$i.'</a>';    
                        } else
                        {
                            $out .= '<a href="'.get_pagenum_link($i).'">'.$i.'</a>';
                        }
                    }  
                    
                    if($last_end != $args['maxpage'])
                    {
                        if($args['maxpage'] - $BEFORE_DOTS > $last_end)
                        {
                            $out .= '<span class="separator">...</span>';
                            
                            for($i = $args['maxpage']-1; $i <= $args['maxpage']; $i++)
                            {
                                if($i == $args['paged'])
                                {
                                    $out .= '<a class="current-page" >'.$i.'</a>';    
                                } else
                                {
                                    $out .= '<a href="'.get_pagenum_link($i).'">'.$i.'</a>';
                                }
                            }                         
                                
                        } else
                        {
                            for($i = $last_end+1; $i <= $args['maxpage']; $i++)
                            {
                                if($i == $args['paged'])
                                {
                                    $out .= '<a class="current-page" >'.$i.'</a>';    
                                } else
                                {
                                    $out .= '<a href="'.get_pagenum_link($i).'">'.$i.'</a>';
                                }
                            }                          
                        }
                    }
       
                } else
                {
                    for($i = 1; $i <= $args['maxpage']; $i++)
                    {
                        if($i == $args['paged'])
                        {
                            $out .= '<a class="current-page" >'.$i.'</a>';    
                        } else
                        {
                            $out .=  '<a href="'.get_pagenum_link($i).'">'.$i.'</a>';
                        }
                    } 
                }
                            
                if($args['paged'] < $args['maxpage'] and $SHOW_PREV_NEXT_BTN)
                {
                    $out .= '<a class="next-btn" href="'.get_pagenum_link($args['paged']+1).'">'.__('Next', CMS_TXT_DOMAIN).'</a>'; 
                }
                           
                $out .= '<div class="dc-clear-both"></div></div>';
            }        
        }  
        
        if($echo) { echo $out; } else { return $out; }              
    }
        

    public function wpPageTitle($args=array(), $echo=true)
    {                    
        $def = array(
            'title' => null,
            'subtitle' => null,
            'tag' => 'h1',
            'callfree' => false // if true function will not try access to page meta data
        );        
        $args = $this->combineArgs($def, $args);   
        
        $out = '';
        if($args['callfree'])
        {     
            if($args['title'] !== null and $args['title'] != '')
            {
                $out .= '<'.$args['tag'].'>';
                    $out .= $args['title'];
                    if($args['subtitle'] !== null and $args['subtitle'] != '')
                    {
                        $out .= '<span>'.$args['subtitle'].'</span>';
                    }
                $out .= '</'.$args['tag'].'>';
            }            
        } else
        {
            global $dc_page_common_opt;             
            global $post;
            
            if($dc_page_common_opt['page_misc_hide_title_cbox'])    
            {
                return;
            }
                            
            if($args['title'] === null)
            {            
                $args['title'] = $post->post_title;
            }                    
            if($args['subtitle'] === null) 
            {
                $args['subtitle'] = $dc_page_common_opt['page_misc_subtitle'];        
            }
                  
            $out .= '<'.$args['tag'].'>';
                $out .= $args['title'];
                if($args['subtitle'] != '' and $dc_page_common_opt['page_misc_subtitle_display_cbox'])
                {
                    $out .= '<span>'.$args['subtitle'].'</span>';
                }
            $out .= '</'.$args['tag'].'>';            
        }                
        
        if($echo) { echo $out; } else { return $out; } 
    }    
 
    public function getPagedQueryVar()
    {
        global $post;
        global $wp_query;
        $paged = 1;
        
        $page_on_front = (int)get_option('page_on_front');
        if($page_on_front != 0 and $page_on_front == $post->ID)
        {
            $paged = isset($wp_query->query_vars['page']) ? $wp_query->query_vars['page'] : 1;  
        } else            
        {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }
        
        return $paged;        
    }
                  
    public function getYearQueryVar() 
    {
        global $wp_query;
         
        $result = '';                
        $result = ($wp_query->query_vars['year']) ? $wp_query->query_vars['year'] : ''; 
        
        return $result;  
    } 

    public function getAuthorSlugQueryVar() 
    {
        global $wp_query;
         
        $result = '';                
        $result = ($wp_query->query_vars['author_name']) ? $wp_query->query_vars['author_name'] : ''; 
        
        return $result;  
    }      
        
    public function getTagIDQueryVar() 
    {
        global $wp_query;
         
        $result = '';                
        $result = ($wp_query->query_vars['tag_id']) ? $wp_query->query_vars['tag_id'] : ''; 
        
        return $result;  
    }     
    
    public function getMonthQueryVar() 
    {
        global $wp_query;
         
        $result = '';                
        $result = ($wp_query->query_vars['monthnum']) ? $wp_query->query_vars['monthnum'] : ''; 
        
        return $result;  
    } 
    
    public function getCatIDQueryVar() 
    {
        global $wp_query;
         
        $result = '';                
        $result = ($wp_query->query_vars['cat']) ? $wp_query->query_vars['cat'] : ''; 
        
        return $result;  
    } 
           
    public function getSearchQueryVar($args=array()) 
    {
        global $wp_query; 
        
        $def = array(
            'empty' => false
        );
        $args = $this->combineArgs($def, $args);
        
        $search = '';
                
        if($wp_query->query_vars['s'])
        {            
            $search = $wp_query->query_vars['s']; 
        } else
        {
            $text = GetDCCPI()->getIGeneral()->getOption('search_dummy_text');
            $search = ($args['empty']) ? '' : $text;
        }
        
        return $search;                                                      
    } 
    
    public function getDefaultHeaderData($echo=true)
    {
        global $dc_is_single; 
        global $dc_is_page; 
        global $dc_post_type; 
        
        $out = ''; 
        
        if(!($dc_is_single and $dc_post_type == 'post') and 
           !($dc_is_page and $dc_post_type == 'page'))
        {
            if(GetDCCPI()->getIGeneral()->getOption('seo_use'))
            {
                $out .= GetDCCPI()->getIGeneral()->getGlobalSEOData();     
            } else
            {
                $out .= '<title>'.get_bloginfo('name').wp_title('-', false).'</title> ';  
            }                         
        } 
         
        if($echo) { echo $out; } else { return $out; }                  
    } 
    
    public function getPostHeaderData($echo=true)
    {
        global $dc_is_single;
        global $dc_post_seo_opt;
        global $dc_post_type;
        
        $out = '';        
        
        if($dc_is_single and ($dc_post_seo_opt !== false) and $dc_post_type == 'post')
        {
            if((GetDCCPI()->getIGeneral()->getOption('seo_use') and GetDCCPI()->getIGeneral()->getOption('seo_overwrite_post')) or
               (!$dc_post_seo_opt['post_seo_active_cbox'] and GetDCCPI()->getIGeneral()->getOption('seo_use')) )
            {
                $out .= GetDCCPI()->getIGeneral()->getGlobalSEOData();     
            } else
            {                                    
                if($dc_post_seo_opt['post_seo_title_use_cbox'] and $dc_post_seo_opt['post_seo_title'] != '')
                {
                    $before = '';
                    if($dc_post_seo_opt['post_seo_add_blog_name_cbox']) { $before = get_bloginfo('name').' - '; }
                    $out .= '<title>'.$before.$dc_post_seo_opt['post_seo_title'].'</title>';      
                } else
                {
                    $out .= '<title>'.get_bloginfo('name').wp_title('-', false).'</title> ';    
                }
                
                if($dc_post_seo_opt['post_seo_keywords_use_cbox'] and $dc_post_seo_opt['post_seo_keywords'] != '')
                {
                    $out .= '<meta name="keywords" content="'.$dc_post_seo_opt['post_seo_keywords'].'" /> ';
                }

                if($dc_post_seo_opt['post_seo_desc_use_cbox'] and $dc_post_seo_opt['post_seo_desc'] != '')
                {
                    $out .= '<meta name="description" content="'.$dc_post_seo_opt['post_seo_desc'].'" /> ';
                }
                
                if($dc_post_seo_opt['post_seo_noindex_cbox'] != false or
                   $dc_post_seo_opt['post_seo_nofollow_cbox'] != false or
                   $dc_post_seo_opt['post_seo_nosnippet_cbox'] != false or
                   $dc_post_seo_opt['post_seo_noodp_cbox'] != false or
                   $dc_post_seo_opt['post_seo_noarchive_cbox'] != false or
                   $dc_post_seo_opt['post_seo_noimageindex_cbox'] != false)
                {            
                    $out .= '<meta name="robots" content="';
                        $comma = false;
                        if($dc_post_seo_opt['post_seo_noindex_cbox'])      { if($comma) { $out .= ', '; } $out .= 'noindex'; $comma = true; }
                        if($dc_post_seo_opt['post_seo_nofollow_cbox'])     { if($comma) { $out .= ', '; } $out .= 'nofollow'; $comma = true; }
                        if($dc_post_seo_opt['post_seo_nosnippet_cbox'])    { if($comma) { $out .= ', '; } $out .= 'nosnippet'; $comma = true; }
                        if($dc_post_seo_opt['post_seo_noodp_cbox'])        { if($comma) { $out .= ', '; } $out .= 'noodp'; $comma = true; }
                        if($dc_post_seo_opt['post_seo_noarchive_cbox'])    { if($comma) { $out .= ', '; } $out .= 'noarchive'; $comma = true; }
                        if($dc_post_seo_opt['post_seo_noimageindex_cbox']) { if($comma) { $out .= ', '; } $out .= 'noimageindex'; $comma = true; } 
                    $out .= '" />';                       
                }
                
                if(is_array($dc_post_seo_opt['post_seo_private_meta']))
                {                                                       
                    foreach($dc_post_seo_opt['post_seo_private_meta'] as $meta)
                    {
                        if(!$meta->_active) { continue; }
                        
                        $meta->_content = str_replace(array("\r\n", "\n", "\r"), ' ', $meta->_content);
                        
                        if($meta->_name != '' and (trim($meta->_content) != ''))
                        {
                            $out .= '<meta name="'.$meta->_name.'" content="'.$meta->_content.'" />';
                    
                        }
                    }    
                }
            
            }
            
        }
        
        if($echo) { echo $out; } else { return $out; }  
    }
    
    public function getPageHeaderData($echo=true)
    {
        global $dc_is_page;
        global $dc_page_seo_opt;
        global $dc_post_type; 
        
        $out = '';        
        
        if($dc_is_page and ($dc_page_seo_opt !== false) and $dc_post_type == 'page')
        {
            if((GetDCCPI()->getIGeneral()->getOption('seo_use') and GetDCCPI()->getIGeneral()->getOption('seo_overwrite_page')) or
               (!$dc_page_seo_opt['page_seo_active_cbox'] and GetDCCPI()->getIGeneral()->getOption('seo_use')) )
            {
                $out .= GetDCCPI()->getIGeneral()->getGlobalSEOData();     
            } else
            {                
            
                if($dc_page_seo_opt['page_seo_title_use_cbox'] and $dc_page_seo_opt['page_seo_title'] != '')
                {
                    $before = '';
                    if($dc_page_seo_opt['page_seo_add_blog_name_cbox']) { $before = get_bloginfo('name').' - '; }
                    $out .= '<title>'.$before.$dc_page_seo_opt['page_seo_title'].'</title>';      
                } else
                {
                    $out .= '<title>'.get_bloginfo('name').wp_title('-', false).'</title> ';    
                }
                
                if($dc_page_seo_opt['page_seo_keywords_use_cbox'] and $dc_page_seo_opt['page_seo_keywords'] != '')
                {
                    $out .= '<meta name="keywords" content="'.$dc_page_seo_opt['page_seo_keywords'].'" /> ';
                }

                if($dc_page_seo_opt['page_seo_desc_use_cbox'] and $dc_page_seo_opt['page_seo_desc'] != '')
                {
                    $out .= '<meta name="description" content="'.$dc_page_seo_opt['page_seo_desc'].'" /> ';
                }
                
                if($dc_page_seo_opt['page_seo_noindex_cbox'] != false or
                   $dc_page_seo_opt['page_seo_nofollow_cbox'] != false or
                   $dc_page_seo_opt['page_seo_nosnippet_cbox'] != false or
                   $dc_page_seo_opt['page_seo_noodp_cbox'] != false or
                   $dc_page_seo_opt['page_seo_noarchive_cbox'] != false or
                   $dc_page_seo_opt['page_seo_noimageindex_cbox'] != false)
                {            
                    $out .= '<meta name="robots" content="';
                        $comma = false;
                        if($dc_page_seo_opt['page_seo_noindex_cbox'])      { if($comma) { $out .= ', '; } $out .= 'noindex'; $comma = true; }
                        if($dc_page_seo_opt['page_seo_nofollow_cbox'])     { if($comma) { $out .= ', '; } $out .= 'nofollow'; $comma = true; }
                        if($dc_page_seo_opt['page_seo_nosnippet_cbox'])    { if($comma) { $out .= ', '; } $out .= 'nosnippet'; $comma = true; }
                        if($dc_page_seo_opt['page_seo_noodp_cbox'])        { if($comma) { $out .= ', '; } $out .= 'noodp'; $comma = true; }
                        if($dc_page_seo_opt['page_seo_noarchive_cbox'])    { if($comma) { $out .= ', '; } $out .= 'noarchive'; $comma = true; }
                        if($dc_page_seo_opt['page_seo_noimageindex_cbox']) { if($comma) { $out .= ', '; } $out .= 'noimageindex'; $comma = true; } 
                    $out .= '" />';                       
                }
                
                if(is_array($dc_page_seo_opt['page_seo_private_meta']))
                {                                                       
                    foreach($dc_page_seo_opt['page_seo_private_meta'] as $meta)
                    {
                        if(!$meta->_active) { continue; }
                        
                        $meta->_content = str_replace(array("\r\n", "\n", "\r"), ' ', $meta->_content);
                        
                        if($meta->_name != '' and (trim($meta->_content) != ''))
                        {
                            $out .= '<meta name="'.$meta->_name.'" content="'.$meta->_content.'" />';
                    
                        }
                    }    
                }
            }
            
        }
        
        if($echo) { echo $out; } else { return $out; }  
    }    
      
    public function wpCategoryLeftSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_category_page'); 
        if($layout == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null, 'slug' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getPostCategorySidebar($args['slug'], CMS_SIDEBAR_LEFT); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd(); 
        }
        
        if($echo) { echo $out; } else { return $out; }           
    }

    public function wpCategoryRightSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_category_page'); 
        if($layout == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null, 'slug' => null);
            $args = $this->combineArgs($def, $args);
   
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getPostCategorySidebar($args['slug'], CMS_SIDEBAR_RIGHT); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
         
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd(); 
        }
        
        if($echo) { echo $out; } else { return $out; }            
    }

    public function wpArchiveLeftSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_archive_page'); 
        if($layout == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_archive_left'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd(); 
        }
        
        if($echo) { echo $out; } else { return $out; }         
    }

    public function wpArchiveRightSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_archive_page'); 
        if($layout == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_archive_right'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd(); 
        }
        
        if($echo) { echo $out; } else { return $out; }             
    }
 
    public function wpPageLeftSidebar($args=array(), $echo=true)
    {
        global $dc_page_common_opt;
        $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
        $args = $this->combineArgs($def, $args);
        
        if($args['layout'] === null) { $args['layout'] = $dc_page_common_opt['page_layout']; }
        if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_page_common_opt['page_sid_left']; }
        
        $out = '';
                
        if($args['layout'] == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $args['layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {        
            $args['side'] = CMS_SIDEBAR_LEFT;
                                 
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);            
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }

     public function wpPageRightSidebar($args=array(), $echo=true)
    {
        global $dc_page_common_opt;
        $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
        $args = $this->combineArgs($def, $args);        
                        
        if($args['layout'] === null) { $args['layout'] = $dc_page_common_opt['page_layout']; } 
        if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_page_common_opt['page_sid_right']; }
        
        $out = '';
        
        if($args['layout'] == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $args['layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {       
            $args['side'] = CMS_SIDEBAR_RIGHT;
            
            $out .= $this->getSidColWrapperClassStart($args['layout']);             
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }
    
    private function getSidColWrapperClassStart($layout)
    {
        $out = '';
        
        if($layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $out .= '<div class="dc-four dc-columns">'; 
        } else
        {
            $out .= '<div class="dc-five dc-columns">'; 
        }     
            
        return $out;     
    }

    private function getSidColWrapperClassEnd()
    {
        $out = '</div>';                    
        return $out;     
    }    
    
    public function getSearchPageLayoutClass($args=array())
    {
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;

        $layout = GetDCCPI()->getIGeneral()->getOption('layout_search_page');
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    
        
        return $class;        
    }       

    public function wpSearchPageLeftSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_search_page'); 
        if($layout == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_search_left'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; }         
    }        
    
    public function wpSearchPageRightSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_search_page'); 
        if($layout == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_search_right'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; }         
    }  
    
    public function get404PageLayoutClass($args=array())
    {
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;

        $layout = GetDCCPI()->getIGeneral()->getOption('layout_404_page');
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    
        
        return $class;        
    }        
    
    public function wp404PageLeftSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_404_page'); 
        if($layout == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_404_left'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']); 
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();  
        }
        
        if($echo) { echo $out; } else { return $out; }         
    }    
          
    public function wp404PageRightSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('layout_404_page'); 
        if($layout == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_404_right'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; }         
    }     
    
    
    public function getArchivePageLayoutClass($args=array())
    {
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;

        $layout = GetDCCPI()->getIGeneral()->getOption('layout_archive_page');
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    
        
        return $class;        
    }
    
    public function getProjectCategoryPageLayoutClass($args=array())
    {
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;

        $layout = GetDCCPI()->getIGeneral()->getOption('project_layout_category_page');
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    
        
        return $class;        
    }        
    
    public function getCategoryPageLayoutClass($args=array())
    {
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;

        $layout = GetDCCPI()->getIGeneral()->getOption('layout_category_page');
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    
        
        return $class;        
    }    
    
    public function getPageLayoutClass($args=array())
    {
        global $dc_page_common_opt;
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;

        if($dc_page_common_opt !== false) { $layout = $dc_page_common_opt['page_layout']; }
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    
        
        return $class;
    }    
    
    public function wpPostLeftSidebar($args=array(), $echo=true)
    {
        global $dc_post_common_opt;
        $out = '';
        
        if($dc_post_common_opt['post_layout'] == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $dc_post_common_opt['post_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_post_common_opt['post_sid_left']; }                   
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_left'); }
            if($args['layout'] === null) { $args['layout'] = $dc_post_common_opt['post_layout']; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }

     public function wpPostRightSidebar($args=array(), $echo=true)
    {
        global $dc_post_common_opt;
        $out = '';
        
        if($dc_post_common_opt['post_layout'] == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $dc_post_common_opt['post_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_post_common_opt['post_sid_right']; }
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_right'); }                
            if($args['layout'] === null) { $args['layout'] = $dc_post_common_opt['post_layout']; }                                           
        
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']); 
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }
    
    public function getPostLayoutClass($args=array())
    {
        global $dc_post_common_opt;
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;

        if($dc_post_common_opt !== false) { $layout = $dc_post_common_opt['post_layout']; }
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    
        
        return $class;
    }           
    
    public function wpPageCustomOptCSS($echo=true)
    {   
        global $dc_is_page;
        global $dc_page_common_opt;
        global $post;
        $out = '';
        
        if($dc_is_page and $post->post_type == 'page')
        {                                                 
            if($dc_page_common_opt !== false)
            {   
                $pct = & $dc_page_common_opt; 
                
                $out .= ' <style type="text/css">';

                if(!GetDCCPI()->getIGeneral()->getOption('bg_force'))
                {
                    if($pct['page_bg_use_cbox'] or (bool)$pct['page_bg_color_use_cbox'])
                    {   
                        $out .= ' body { ';
                            if($pct['page_bg_use_cbox'])
                            {
                                $out .= 'background-image:url('.$pct['page_bg_image'].');';
                                $out .= 'background-repeat:'.$pct['page_bg_repeat'].';';
                                $out .= 'background-attachment:'.$pct['page_bg_attachment'].';';
                                
                                $pos_x = $pct['page_bg_pos_x'];
                                $pos_y = $pct['page_bg_pos_y'];
                                if($pct['page_bg_pos_x_px_use_cbox']) { $pos_x = $pct['page_bg_pos_x_px'].'px'; }
                                if($pct['page_bg_pos_y_px_use_cbox']) { $pos_y = $pct['page_bg_pos_y_px'].'px'; }
                                
                                $out .= 'background-position:'.$pos_x.' '.$pos_y.';';
                            }
                            
                            if($pct['page_bg_color_use_cbox'])
                            {
                                $out .= 'background-color:'.$pct['page_bg_color'].';';    
                            } 
                        $out .= ' } ';        
                    }
                }
                $out .= ' </style> ';
            }
        
        }
        
        if($echo) { echo $out; } else { return $out; }
    }
    
    public function wpPostCustomOptCSS($echo=true)
    {   
        global $dc_is_single;
        global $dc_post_common_opt;
        global $post;
        $out = '';
        
        if($dc_is_single and $post->post_type == 'post')
        {                                                 
            if($dc_post_common_opt !== false)
            {   
                $pct = & $dc_post_common_opt; 
                
                $out .= ' <style type="text/css">';

                if(!GetDCCPI()->getIGeneral()->getOption('bg_force'))
                {                
                    if($pct['post_bg_use_cbox'] or (bool)$pct['post_bg_color_use_cbox'])
                    {   
                        $out .= ' body { ';
                            if($pct['post_bg_use_cbox'])
                            {
                                $out .= 'background-image:url('.$pct['post_bg_image'].');';
                                $out .= 'background-repeat:'.$pct['post_bg_repeat'].';';
                                $out .= 'background-attachment:'.$pct['post_bg_attachment'].';';
                                
                                $pos_x = $pct['post_bg_pos_x'];
                                $pos_y = $pct['post_bg_pos_y'];
                                if($pct['post_bg_pos_x_px_use_cbox']) { $pos_x = $pct['post_bg_pos_x_px'].'px'; }
                                if($pct['post_bg_pos_y_px_use_cbox']) { $pos_y = $pct['post_bg_pos_y_px'].'px'; }
                                
                                $out .= 'background-position:'.$pos_x.' '.$pos_y.';';
                            }
                            
                            if($pct['post_bg_color_use_cbox'])
                            {
                                $out .= 'background-color:'.$pct['post_bg_color'].';';    
                            } 
                        $out .= ' } ';        
                    }
                }
                $out .= ' </style> ';
            }
        
        }
        
        if($echo) { echo $out; } else { return $out; }
    }      
 
    public function wpPostFull($echo=true)
    {
        global $dc_post;
        global $dc_post_opt;
        global $page, $pages, $multipage, $numpages;         

        $out = '';
        
        $out .= '<div class="blog-post-full-wrapper">';
        
            $date_display = GetDCCPI()->getIGeneral()->getOption('blog_full_ib_date_display');
            $date_under_title_display = GetDCCPI()->getIGeneral()->getOption('blog_full_date_under_title_display');  
            $date_format = GetDCCPI()->getIGeneral()->getOption('blog_full_ib_date_format');            
            $author_display = GetDCCPI()->getIGeneral()->getOption('blog_full_ib_author_display');
            $categories_display = GetDCCPI()->getIGeneral()->getOption('blog_full_ib_categories_display');
            $comments_display = GetDCCPI()->getIGeneral()->getOption('blog_full_ib_comments_display');
            $tags_display = GetDCCPI()->getIGeneral()->getOption('blog_full_ib_tags_display');
            $post_permalink = get_permalink($dc_post->ID);
            $year  = mysql2date('Y', $dc_post->post_date_gmt, true);
            $month  = mysql2date('n', $dc_post->post_date_gmt, true);
        
            // title
            $out .= '<div class="bpf-title-wrapper">';
                $out .= '<h1 class="bpf-title">'.$dc_post->post_title.'</h1>';
                
                // posted date
                if($date_under_title_display)
                {
                    $out .= '<div class="bpf-posted-date">';                 
                        $out .= '<a href="'.get_month_link($year, $month).'">'.__('Posted on', CMS_TXT_DOMAIN).' '.mysql2date($date_format, $dc_post->post_date_gmt).'</a>';  
                    $out .= '</div>'; 
                }
            $out .= '</div>';
        
            // image
            $post_image = $dc_post_opt['post_image'];
            if(GetDCCPI()->getIGeneral()->getOption('theme_use_post_wp_thumbnail'))
            {
                $t_url = GetDCCPI()->getIRenderer()->getPostThumbnailURL($dc_post->ID);
                if($t_url !== false)
                {
                    $post_image = $t_url;                    
                }           
            }   
            if($dc_post_opt['post_image_hide_cbox']) { $post_image = ''; }             
	    if (in_category('pr', $dc_post)) {
	      $post_image = '';
	   }
            
            $post_video_url = $dc_post_opt['post_video_url']; 
            $is_vimeo = strstr($post_video_url, 'vimeo.com') !== false ? true : false;
            $is_youtube = strstr($post_video_url, 'youtube.com') !== false ? true : false;
            
            if(($is_vimeo or $is_youtube) and $dc_post_opt['post_video_display_cbox'])
            {    
                $out .= '<div class="bpf-video-wrapper">';
                    $time = time();
                    if($is_vimeo)
                    {
                        $pos = strrpos($post_video_url, '/') + 1;
                        $video_id = substr($post_video_url, $pos);
                        $out .= '<iframe src="http://player.vimeo.com/video/'.$video_id.'?dc_param='.$time.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                    } else
                    if($is_youtube)
                    {
                        $url = parse_url($post_video_url);
                        $vars = array();
                        parse_str($url['query'], $vars);
                        $out .= '<iframe src="http://www.youtube.com/embed/'.$vars['v'].'?rel=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>';
                    }                                       
                $out .= '</div>';
                if($dc_post_opt['post_video_desc'] != '' and $dc_post_opt['post_video_desc_display_cbox'])
                {
                    $out .= '<div class="bpf-video-description">'.$dc_post_opt['post_video_desc'].'</div>';    
                }                          
            } else                                        
            if($post_image != '')
            {
                $size = dcf_getImageSize($post_image); 
                
                $out .= '<div class="bpf-image-wrapper" style="max-width:'.$size['w'].'px;">';
                    
                    $alt = $dc_post_opt['post_image_alt'];
                    $alt = str_replace(array('"'), '', $alt); 

                    $out .= '<a class="bpf-image-loader async-img-none" rel="'.dcf_getImageURL($post_image, $size['w'], $size['h'], CMS_IMAGE_NOCROP).'" title="'.$alt.'"></a>';    
                
                    $image_desc = trim($dc_post_opt['post_image_desc']); 
                    if($image_desc != '' and $dc_post_opt['post_image_desc_display_cbox'])
                    {
                        $out .= '<span class="bpf-image-desc">'.$image_desc.'</span>';
                    }
                $out .= '</div>';
            }
            
            // information bar
            if($date_display or $author_display or $categories_display or $comments_display or $tags_display)
            {
                $out .= '<ul class="bpf-info-bar">';
                     
                    if($date_display)
                    {                       
                        $out .= '<li class="date"><a href="'.get_month_link($year, $month).'">'.mysql2date($date_format, $dc_post->post_date_gmt).'</a></li>';  
                    }
                    
                    if($comments_display and $dc_post->comment_status == 'open') 
                    {    
                        $out .= '<li class="comments">';  
                            $text = '';
                            if($dc_post->comment_count == 0) { $text = __('No comments', CMS_TXT_DOMAIN); } 
                            else if($dc_post->comment_count == 1) { $text = __('One comment', CMS_TXT_DOMAIN); } 
                            else { $text = $dc_post->comment_count.'&nbsp;'.__('comments', CMS_TXT_DOMAIN); }           
                                 
                            $out .= '<a href="'.get_comments_link($dc_post->ID).'" class="comments">'.$dc_post->comment_count.'</a>';
                        $out .= '</li>';
                    }                              
                    
                    if($author_display)  
                    {                              
                        $out .= '<li class="author">'.__('by', CMS_TXT_DOMAIN).'&nbsp;<a href="'.get_author_posts_url($dc_post->post_author).'" class="author">'.get_the_author_meta('display_name', $dc_post->post_author).'</a>';
                        if(is_array($dc_post_opt['post_authors_arr']))
                        {   
                            foreach($dc_post_opt['post_authors_arr'] as $author)
                            {
                                if($author != $dc_post->post_author)
                                {
                                    $user_data = GetDCCPI()->getICache()->get_wp_user_by_id($author);
                                    if($user_data !== false)
                                    {
                                        $out .= ', ';
                                        $out .= '<a href="'.get_author_posts_url($user_data->ID).'" class="author">'.$user_data->display_name.'</a>';                    
                                    }
                                }
                            }
                        }
                        $out .= '</li>';
                    }
                    
                    if($categories_display)
                    {    
                        $catlist = wp_get_object_terms($dc_post->ID, 'category');
                        
                        if(is_array($catlist) and count($catlist))
                        {                            
                            $count = count($catlist); 
                            $out .= '<li class="categories">';
                                for($i = 0; $i < $count; $i++)
                                {
                                    if($i > 0) { $out .= ', '; }
                                    $cat = get_category($catlist[$i]);
                                    $out .= '<a href="'.get_category_link($catlist[$i]).'" >'.$cat->name.'</a>';
                                     
                                }
                            $out .= '</li>';
                        }                  
                    }    
                    

                   
                    if($tags_display)
                    {
                        $taglist = wp_get_object_terms($dc_post->ID, 'post_tag');                              

                        if(is_array($taglist))
                        {   
                           $count = count($taglist);                                   
                           if($count > 0)
                           { 
                               $out .= '<li class="tags">';
                               
                               $i = 0;            
                               foreach($taglist as $tag)
                               {   
                                   if($i > 0)
                                   {
                                       $out .= ', ';
                                   }
                                   
                                   $title = '';
                                   if($tag->count == 1) { $title = 'One post'; } else { $title = $tag->count.' posts'; }                                            
                                   $out .= '<a href="'.get_tag_link($tag->term_id).'" title="'.$title.'">'.$tag->name.'</a>';
                                   $i++;
                               }                       
                               $out .= '</li>';                                            
                           } 
                        }
                    }                                                      
                    
                    $out .= '<div class="dc-clear-both"></div>';
                $out .= '</ul>';
            }            
            
            // content
            $out .= '<div class="bpf-content">';  
                if($multipage)
                {  
                    $out .= apply_filters('the_content', $pages[$page-1]);            
                } else
                {
                    $out .= apply_filters('the_content', $dc_post->post_content);        
                }                                        
                $out .= '<div class="dc-clear"></div>';                        
            $out .= '</div>';
            $out .= GetDCCPI()->getIRenderer()->wpPaginationBlock(false);
                      
            // about author
            if(GetDCCPI()->getIGeneral()->getOption('blog_full_about_user_display'))
            {         
                $out .= '<div class="bpf-about-user-wrapper">';                                                  
                    $out .= $this->getAboutPostUser(array('ID' => $dc_post->post_author));
                    
                    if(GetDCCPI()->getIGeneral()->getOption('blog_full_about_extra_users_display'))
                    {
                        if(is_array($dc_post_opt['post_authors_arr']))
                        {             
                            foreach($dc_post_opt['post_authors_arr'] as $author)
                            {   
                                $out .= $this->getAboutPostUser(array('ID' => $author, 'margin' => true));          
         
                            } 
                        }
                    }
                $out .= '</div>';
            }    
           
            // related posts  
            if(GetDCCPI()->getIGeneral()->getOption('blog_related_display'))
            {
                $tax = GetDCCPI()->getIGeneral()->getOption('blog_related_taxonomy'); 
                $count = GetDCCPI()->getIGeneral()->getOption('blog_related_count');
                
                $terms = wp_get_object_terms($dc_post->ID, $tax); 
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                }
                else    
                {            
                    $temp = array();                
                    foreach($terms as $t){ array_push($temp, $t->term_id); }              
                    $terms = $temp;
                }
                
                if(count($terms))
                {
                    $query_args = array('post_type' => 'post', 'posts_per_page' => $count, 
                        'paged' => 1, 'nopaging' => false, 
                        'post_status' => 'publish', 'ignore_sticky_posts' => false, 'post__not_in' => array($dc_post->ID));
                         
                    $query_args['tax_query'] = array(
                        array(
                            'taxonomy' => $tax,
                            'field' => 'id',
                            'terms' => $terms,
                            'operator' => 'IN'
                        )); 
                        
                    $dc_query = new WP_Query($query_args);
                    if($dc_query->post_count)
                    {
                        $out .= '<div class="bpf-related">';
                            $out .= '<h4 class="head">'.__('Related posts', CMS_TXT_DOMAIN).'</h4>';
                                              

                            $counter = 0;
                            for($i = 0; $i < $dc_query->post_count; $i++)
                            {
                                $p = & $dc_query->posts[$i]; 
                                $p_opt = get_post_meta($p->ID, 'post_opt', true);
                                $p_permalink = get_permalink($p->ID);
                            
                                $image_desc = trim($p_opt['post_image_desc']);
                                $alt = '';
                                if($p_opt['post_image_desc_display_cbox'])
                                {
                                    $alt = str_replace(array('"'), '', $image_desc);
                                }                        
                            
                                $class = 'cell';
                                $clear = false;
                                if($counter == 3) { $class = 'cell-last'; $counter = 0; $clear = true; }                            
                                                                   
                                $out .= '<div class="'.$class.'">';
                                 
                                    // image
                                    $post_image = $p_opt['post_image'];
                                    if(GetDCCPI()->getIGeneral()->getOption('theme_use_post_wp_thumbnail'))
                                    {
                                        $t_url = GetDCCPI()->getIRenderer()->getPostThumbnailURL($p->ID);
                                        if($t_url !== false)
                                        {
                                            $post_image = $t_url;                    
                                        }           
                                    }    
                                    if($p_opt['post_image_hide_cbox']) { $post_image = ''; }                              
                                 
                                    if($post_image != '')
                                    {
                                        $out .= '<a class="image-loader async-img-none" href="'.$p_permalink.'" rel="'.dcf_getImageURL($post_image, 200, 200, CMS_IMAGE_CROP_FIT).'" title="'.$alt.'"></a>';
                                    }  
                                    
                                    $out .= '<div class="bpf-related-post-title"><a href="'.$p_permalink.'">'.$p->post_title.'</a></div>';
                                $out .= '</div>';
                                if($clear) { $out .= '<div class="dc-clear-both"></div>';  }
                                
                                if(!$clear) { $counter++; }
                            }
                            
                            $out .= '<div class="dc-clear-both"></div>'; 
                        $out .= '</div>';
                    }
                }                                   
                   
            }                          
            
        $out .= '</div>';
        
        if($echo) { echo $out; } else { return $out; }
    }
    
    private function getAboutPostUser($args=array(), $echo=false)
    {
        $def = array(
            'ID' => null,
            'margin' => false
        );
        
        $args = $this->combineArgs($def, $args);
        
        $out = '';
        
        if($args !== null)
        {
            $user = get_userdata($args['ID']);
            if(is_object($user))
            {                                  
                $add_class = '';
                if($args['margin']) { $add_class = 'bpf-about-user-margin-top'; }
                
                $out .= '<div class="bpf-about-user '.$add_class.'">';
                
                    $user_bio = get_the_author_meta('description', $user->ID);
                    $user_avatar_url = get_avatar($user->user_email, '50'); 
                
                    $out .= '<div class="left-side">';                            
                        
                        $user_url = '';                            
                        if($user->user_url != '') { $user_url = ' href="'.$user->user_url.'" target="_blank" '; }
                        $out .= '<a '.$user_url.' class="avatar">';
                            $out .= $user_avatar_url;       
                        $out .= '</a>';                            
                        
                    $out .= '</div>';
                    
                    $out .= '<div class="right-side">';
                        $out .= '<div class="user-name">';
                            $out .= $user->display_name;
                        $out .= '</div>';
                        $out .= $user_bio;  
                        
                        $out .= '<div class="user-links">';
                            $out .= '<a href="'.get_author_posts_url($user->ID).'" >'.__('View all author posts', CMS_TXT_DOMAIN).'</a>';
                            if($user->user_url != '')
                            {
                                $out .= '<a href="'.$user->user_url.'" >'.__('Read more about author', CMS_TXT_DOMAIN).'</a>';
                            }
                        $out .= '</div>';  
                    $out .= '</div>';
                    
                    $out .= '<div class="dc-clear-both"></div>';
                $out .= '</div>';                
            }
        }         
    
        if($echo) { echo $out; } else { return $out; }     
    }
    

    /**
     * Return post thumbnail URL to full size image
     * @param int post ID
     * 
     * @return URL to image or false in thumbnail not exist
     */       
    public function getPostThumbnailURL($id)
    {
        $new_url = false;
        
        $t_id = get_post_thumbnail_id($id);
        if($t_id !== null)
        {
            $t_url = wp_get_attachment_url($t_id);
            if($t_url !== false)
            {
                $new_url = $t_url;
            } 
        }    
        
        return $new_url;        
    }
    
    /**
     * Render WP post in short mode
     * @param args = {id=null, meta=null, post=null, layout=null} 
     * @param echo = Return or output HTML code
     * @return string HTML code
     */  
    public function wpPostShort($args=array(), $echo=false)
    {
        $def = array(
            'id' => null,
            'meta' => null,
            'post' => null,
            'layout' => null // normal, compact
        );
        $args = $this->combineArgs($def, $args);

        if($args['post'] === null and $args['id'] !== null)
        {
            global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = ".$args['id']." AND post_status = 'publish' AND post_type = 'post' ");
            if(is_array($result))
            {
                $p = new DCC_WPPost($result[0]);
                $args['post'] = $p;
                $opt = get_post_meta($p->ID, 'post_opt', true);
                $args['meta'] = $opt; 
            }   
        } else                   
        if(is_object($args['post']) and $args['meta'] ===  null)
        {
            $opt = get_post_meta($args['post']->ID, 'post_opt', true);
            $args['meta'] = $opt;            
        }             
     
        // check valid layout name
        if($args['layout'] != 'normal' and $args['layout'] != 'compact') { $args['layout'] = null; }
        // if no layout selected assign layout from post settings
        if($args['layout'] === null) { $args['layout'] = $args['meta']['post_misc_layout']; }
     
        $out = '';
        switch($args['layout'])
        {
            case 'normal':
            {
                $p_args = array(
                    'id' => $args['id'],
                    'meta' => $args['meta'],
                    'post' => $args['post']
                );                
                $out .= $this->wpPostShortLayoutNormal($p_args);
            }
            break;    
            
            case 'compact':
            {
                $p_args = array(
                    'id' => $args['id'],
                    'meta' => $args['meta'],
                    'post' => $args['post']
                );                
                $out .= $this->wpPostShortLayoutCompact($p_args);
            }
            break;  
            
            default:
            {
                $p_args = array(
                    'id' => $args['id'],
                    'meta' => $args['meta'],                
                    'post' => $args['post']
                );                
                $out .= $this->wpPostShortLayoutNormal($p_args);
            }
            break;                           
        }      
        
        if($echo) { echo $out; } else { return $out; }        
    }
 
    public function wpPostShortLayoutNormal($args=array(), $echo=false)
    {
        $def = array(
            'id' => null,
            'meta' => null,
            'post' => null
        );
        $args = $this->combineArgs($def, $args); 
        
        if($args['post'] === null and $args['id'] !== null)
        {
            global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = ".$args['id']." AND post_status = 'publish' AND post_type = 'post' ");
            if(is_array($result))
            {
                $p = new DCC_WPPost($result[0]);
                $args['post'] = $p;
                $opt = get_post_meta($p->ID, 'post_opt', true);
                $args['meta'] = $opt; 
            }   
        } else                   
        if(is_object($args['post']) and $args['meta'] ===  null)
        {
            $opt = get_post_meta($args['post']->ID, 'post_opt', true);
            $args['meta'] = $opt;            
        }        
        
        $dc_post = $args['post'];
        $post_opt = $args['meta'];         
        
        $out = '';
        $out .= '<div class="blog-post-short-wrapper">';
            
            $date_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_display');
            $date_under_title_display = GetDCCPI()->getIGeneral()->getOption('blog_date_under_title_display'); 
            $date_format = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_format');            
            $author_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_author_display');
            $categories_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_categories_display');
            $comments_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_comments_display');
            $tags_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_tags_display');
            $post_permalink = get_permalink($dc_post->ID);
            $year  = mysql2date('Y', $dc_post->post_date_gmt, true);
            $month  = mysql2date('n', $dc_post->post_date_gmt, true);
            
            $out .= '<div class="bps-title-wrapper">';
                // title
                $out .= '<h2 class="bps-title"><a href="'.$post_permalink.'">'.$dc_post->post_title.'</a></h2>';
                
                // posted date
                if($date_under_title_display)
                {
                    $out .= '<div class="bps-posted-date">';                 
                        $out .= '<a href="'.get_month_link($year, $month).'">'.__('Posted on', CMS_TXT_DOMAIN).' '.mysql2date($date_format, $dc_post->post_date_gmt).'</a>';  
                    $out .= '</div>';
                }            
            $out .= '</div>';
                      
            // image
            $post_image = $post_opt['post_image'];
            if(GetDCCPI()->getIGeneral()->getOption('theme_use_post_wp_thumbnail'))
            {
                $t_url = $this->getPostThumbnailURL($dc_post->ID);
                if($t_url !== false)
                {
                    $post_image = $t_url;
                }              
            }
            
            if($post_opt['post_image_hide_cbox']) { $post_image = ''; }
            
            $post_video_url = $post_opt['post_video_url']; 
            $is_vimeo = strstr($post_video_url, 'vimeo.com') !== false ? true : false;
            $is_youtube = strstr($post_video_url, 'youtube.com') !== false ? true : false;
            
            if(($is_vimeo or $is_youtube) and $post_opt['post_video_display_cbox'])
            {    
                $out .= '<div class="bps-video-wrapper">';
                    $time = time();
                    if($is_vimeo)
                    {
                        $pos = strrpos($post_video_url, '/') + 1;
                        $video_id = substr($post_video_url, $pos);
                        $out .= '<iframe src="http://player.vimeo.com/video/'.$video_id.'?dc_param='.$time.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                    } else
                    if($is_youtube)
                    {
                        $url = parse_url($post_video_url);
                        $vars = array();
                        parse_str($url['query'], $vars);
                        $out .= '<iframe src="http://www.youtube.com/embed/'.$vars['v'].'?rel=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>';
                    }                    
                $out .= '</div>';  
                if($post_opt['post_video_desc'] != '' and $post_opt['post_video_desc_display_cbox'])
                {
                    $out .= '<div class="bps-video-description">'.$post_opt['post_video_desc'].'</div>';    
                }  
            } else            
            if($post_image != '')
            {
                $size = dcf_getImageSize($post_image);
                
                $out .= '<div class="bps-image-wrapper" style="max-width:'.$size['w'].'px;">';
                    $alt = $post_opt['post_image_alt'];
                    $alt = str_replace(array('"'), '', $alt); 

                    $out .= '<a class="bps-image-loader async-img-none" href="'.$post_permalink.'" rel="'.dcf_getImageURL($post_image, $size['w'], $size['h'], CMS_IMAGE_NOCROP, $post_opt['post_image_filter']).'" title="'.$alt.'"></a>';    
                
                    $image_desc = trim($post_opt['post_image_desc']);
                    if($image_desc != '' and $post_opt['post_image_desc_display_cbox'])
                    {
                        $out .= '<span class="bps-image-desc">'.$image_desc.'</span>';
                    }
                $out .= '</div>';
            }
        
            $out .= '<div class="bps-content">';
 
                $out .= '<div class="bps-right">';
                
                    // information bar
                    if($date_display or $author_display or $categories_display or $comments_display or $tags_display)
                    {
                        $out .= '<div class="bps-info-bar">';
                            
                            if($date_display)
                            {                       
                                $out .= '<div class="date"><a href="'.get_month_link($year, $month).'">'.mysql2date($date_format, $dc_post->post_date_gmt).'</a></div>';  
                            }
                            
                            if($comments_display and $dc_post->comment_status == 'open') 
                            {    
                                $out .= '<div class="comments">';  
                                    $text = '';
                                    if($dc_post->comment_count == 0) { $text = __('No comments', CMS_TXT_DOMAIN); } 
                                    else if($dc_post->comment_count == 1) { $text = __('One comment', CMS_TXT_DOMAIN); } 
                                    else { $text = $dc_post->comment_count.'&nbsp;'.__('comments', CMS_TXT_DOMAIN); }           
                                         
                                    $out .= '<a href="'.get_comments_link($dc_post->ID).'" class="comments">'.$dc_post->comment_count.'</a>';
                                $out .= '</div>';
                            }                              
                            
                            if($author_display)  
                            {                              
                                $out .= '<div class="author">'.__('by', CMS_TXT_DOMAIN).'&nbsp;<a href="'.get_author_posts_url($dc_post->post_author).'" class="author">'.get_the_author_meta('display_name', $dc_post->post_author).'</a>';
                                if(is_array($post_opt['post_authors_arr']))
                                {   
                                    foreach($post_opt['post_authors_arr'] as $author)
                                    {
                                        if($author != $dc_post->post_author)
                                        {
                                            $user_data = GetDCCPI()->getICache()->get_wp_user_by_id($author);
                                            if($user_data !== false)
                                            {
                                                $out .= ', ';
                                                $out .= '<a href="'.get_author_posts_url($user_data->ID).'" class="author">'.$user_data->display_name.'</a>';                    
                                            }
                                        }
                                    }
                                }
                                $out .= '</div>';
                            }
                            
                            if($categories_display)
                            {    
                                $catlist = wp_get_object_terms($dc_post->ID, 'category');
                                $count = count($catlist);
                                if($count > 0)
                                {
                                    $out .= '<div class="categories">';
                                        for($i = 0; $i < $count; $i++)
                                        {
                                            if($i > 0) { $out .= ', '; }
                                            $cat = get_category($catlist[$i]);
                                            $out .= '<a href="'.get_category_link($catlist[$i]).'" >'.$cat->name.'</a>';
                                             
                                        }
                                    $out .= '</div>';
                                }                  
                            }    
                            

                            
                            if($tags_display)
                            {
                                $taglist = wp_get_object_terms($dc_post->ID, 'post_tag');                              

                                if(is_array($taglist))
                                {   
                                   $count = count($taglist);                                   
                                   if($count > 0)
                                   { 
                                       $out .= '<div class="tags">';
                                       
                                       $i = 0;            
                                       foreach($taglist as $tag)
                                       {   
                                           if($i > 0)
                                           {
                                               $out .= ', ';
                                           }
                                           
                                           $title = '';
                                           if($tag->count == 1) { $title = 'One post'; } else { $title = $tag->count.' posts'; }                                            
                                           $out .= '<a href="'.get_tag_link($tag->term_id).'" title="'.$title.'">'.$tag->name.'</a>';
                                           $i++;
                                       }                       
                                       $out .= '</div>';                                            
                                   } 
                                }
                            }                                                      
                            
                            $out .= '<div class="dc-clear-both"></div>';
                        $out .= '</div>';
                    }    
                $out .= '</div>';
                
                $out .= '<div class="bps-left">';
                    if($dc_post->post_excerpt != '')
                    {                               
                        $out .= apply_filters('the_content', $dc_post->post_excerpt);        
                        $out .= '<a class="bsp-more-link" href="'.$post_permalink.'" >'.__('Read more', CMS_TXT_DOMAIN).'</a>';  
                    } else
                    {                                
                        $content = strstr($dc_post->post_content, '<!--more-->', true);
                        if($content === false) { $content = $dc_post->post_content; }
                        $out .= apply_filters('the_content', $content);
                        $out .= '<a class="bsp-more-link" href="'.$post_permalink.'" >'.__('Read more', CMS_TXT_DOMAIN).'</a>';
                    }                   
                $out .= '</div>';
                
                $out .= '<div class="dc-clear-both"></div>';                
            $out .= '</div>';
        
        $out .= '</div>';
        
        if($echo) { echo $out; } else { return $out; }          
    }

    public function wpPostShortLayoutCompact($args=array(), $echo=false)
    {
        $def = array(
            'id' => null,
            'meta' => null,        
            'post' => null
        );
        $args = $this->combineArgs($def, $args); 
        
        if($args['post'] === null and $args['id'] !== null)
        {
            global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = ".$args['id']." AND post_status = 'publish' AND post_type = 'post' ");
            if(is_array($result))
            {
                $p = new DCC_WPPost($result[0]);
                $args['post'] = $p;
                $opt = get_post_meta($p->ID, 'post_opt', true);
                $args['meta'] = $opt; 
            }   
        } else                   
        if(is_object($args['post']) and $args['meta'] ===  null)
        {
            $opt = get_post_meta($args['post']->ID, 'post_opt', true);
            $args['meta'] = $opt;            
        }        
        
        $dc_post = $args['post'];
        $post_opt = $args['meta'];        
        
        $out = '';
        $out .= '<div class="blog-post-short-compact-wrapper">';
            
            $date_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_display');
            $date_under_title_display = GetDCCPI()->getIGeneral()->getOption('blog_date_under_title_display'); 
            $date_format = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_format');            
            $author_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_author_display');
            $categories_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_categories_display');
            $comments_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_comments_display');
            $tags_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_tags_display');
            $post_permalink = get_permalink($dc_post->ID);
            $year  = mysql2date('Y', $dc_post->post_date_gmt, true);
            $month  = mysql2date('n', $dc_post->post_date_gmt, true);
            
            $out .= '<div class="bps-content">';            
                      
                // image                    
                $post_image = $post_opt['post_image'];
                if(GetDCCPI()->getIGeneral()->getOption('theme_use_post_wp_thumbnail'))
                {
                    $t_url = $this->getPostThumbnailURL($dc_post->ID);
                    if($t_url !== false)
                    {
                        $post_image = $t_url;
                    }              
                }
                
                if($post_opt['post_image_hide_cbox']) { $post_image = ''; }   

                $post_video_url = $post_opt['post_video_url']; 
                $is_vimeo = strstr($post_video_url, 'vimeo.com') !== false ? true : false;
                $is_youtube = strstr($post_video_url, 'youtube.com') !== false ? true : false;
                   
                $is_right_side = false;   
                if((($is_vimeo or $is_youtube) and $post_opt['post_video_display_cbox']) or ($post_image != '')) { $is_right_side = true; }
                   
                // right side
                if($is_right_side)
                {
                    $out .= '<div class="bps-right">';                                    
                        
                        if(($is_vimeo or $is_youtube) and $post_opt['post_video_display_cbox'])
                        {    
                            $out .= '<div class="bps-video-wrapper">';
                                $time = time();
                                if($is_vimeo)
                                {
                                    $pos = strrpos($post_video_url, '/') + 1;
                                    $video_id = substr($post_video_url, $pos);
                                    $out .= '<iframe src="http://player.vimeo.com/video/'.$video_id.'?dc_param='.$time.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                                } else
                                if($is_youtube)
                                {
                                    $url = parse_url($post_video_url);
                                    $vars = array();
                                    parse_str($url['query'], $vars);
                                    $out .= '<iframe src="http://www.youtube.com/embed/'.$vars['v'].'?rel=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>';
                                }                    
                            $out .= '</div>';  
                            if($post_opt['post_video_desc'] != '' and $post_opt['post_video_desc_display_cbox'])
                            {
                                $out .= '<div class="bps-video-description">'.$post_opt['post_video_desc'].'</div>';    
                            }  
                        } else            
                        if($post_image != '')
                        {
                            $size = dcf_getImageSize($post_image);
                            
                            $out .= '<div class="bps-image-wrapper" style="max-width:'.$size['w'].'px;">';
                                $alt = $post_opt['post_image_alt'];
                                $alt = str_replace(array('"'), '', $alt); 

                                $out .= '<a class="bps-image-loader async-img-none" href="'.$post_permalink.'" rel="'.dcf_getImageURL($post_image, $size['w'], $size['h'], CMS_IMAGE_NOCROP, $post_opt['post_image_filter']).'" title="'.$alt.'"></a>';    
                            
                            $out .= '</div>';
                            
                            $image_desc = trim($post_opt['post_image_desc']);
                            if($image_desc != '' and $post_opt['post_image_desc_display_cbox'])
                            {
                                $out .= '<div class="bps-image-desc">'.$image_desc.'</div>';
                            }                        
                        }                                                
                    
                    $out .= '</div>'; 
                }             
            
                // left side
                $class = '';
                if(!$is_right_side) { $class = ' full-width'; }
                $out .= '<div class="bps-left'.$class.'">';
                    
                    // wrapper                    
                    $out .= '<div class="bps-title-wrapper">';   
                        // title            
                        $out .= '<h2 class="bps-title '.$class.'"><a href="'.$post_permalink.'">'.$dc_post->post_title.'</a></h2>';
                        
                        // posted date
                        if($date_under_title_display)
                        {
                            $out .= '<div class="bps-posted-date">';                 
                                $out .= '<a href="'.get_month_link($year, $month).'">'.__('Posted on', CMS_TXT_DOMAIN).' '.mysql2date($date_format, $dc_post->post_date_gmt).'</a>';  
                            $out .= '</div>';
                        }
                    $out .= '</div>';  
                    
                    // information bar
                    if($date_display or $author_display or $categories_display or $comments_display or $tags_display)
                    {
                        $out .= '<div class="bps-info-bar">';
                            
                            if($date_display)
                            {                       
                                $out .= '<div class="date"><a href="'.get_month_link($year, $month).'">'.mysql2date($date_format, $dc_post->post_date_gmt).'</a></div>';  
                            }
                            
                            if($comments_display and $dc_post->comment_status == 'open') 
                            {    
                                $out .= '<div class="comments">';  
                                    $text = '';
                                    if($dc_post->comment_count == 0) { $text = __('No comments', CMS_TXT_DOMAIN); } 
                                    else if($dc_post->comment_count == 1) { $text = __('One comment', CMS_TXT_DOMAIN); } 
                                    else { $text = $dc_post->comment_count.'&nbsp;'.__('comments', CMS_TXT_DOMAIN); }           
                                         
                                    $out .= '<a href="'.get_comments_link($dc_post->ID).'" class="comments">'.$dc_post->comment_count.'</a>';
                                $out .= '</div>';
                            }                              
                            
                            if($author_display)  
                            {                              
                                $out .= '<div class="author">'.__('by', CMS_TXT_DOMAIN).'&nbsp;<a href="'.get_author_posts_url($dc_post->post_author).'" class="author">'.get_the_author_meta('display_name', $dc_post->post_author).'</a>';
                                if(is_array($post_opt['post_authors_arr']))
                                {   
                                    foreach($post_opt['post_authors_arr'] as $author)
                                    {
                                        if($author != $dc_post->post_author)
                                        {
                                            $user_data = GetDCCPI()->getICache()->get_wp_user_by_id($author);
                                            if($user_data !== false)
                                            {
                                                $out .= ', ';
                                                $out .= '<a href="'.get_author_posts_url($user_data->ID).'" class="author">'.$user_data->display_name.'</a>';                    
                                            }
                                        }
                                    }
                                }
                                $out .= '</div>';
                            }
                            
                            if($categories_display)
                            {    
                                $catlist = wp_get_object_terms($dc_post->ID, 'category');
                                $count = count($catlist);
                                if($count > 0)
                                {
                                    $out .= '<div class="categories">';
                                        for($i = 0; $i < $count; $i++)
                                        {
                                            if($i > 0) { $out .= ', '; }
                                            $cat = get_category($catlist[$i]);
                                            $out .= '<a href="'.get_category_link($catlist[$i]).'" >'.$cat->name.'</a>';
                                             
                                        }
                                    $out .= '</div>';
                                }                  
                            }    
                            

                            
                            if($tags_display)
                            {
                                $taglist = wp_get_object_terms($dc_post->ID, 'post_tag');                              

                                if(is_array($taglist))
                                {   
                                   $count = count($taglist);                                   
                                   if($count > 0)
                                   { 
                                       $out .= '<div class="tags">';
                                       
                                       $i = 0;            
                                       foreach($taglist as $tag)
                                       {   
                                           if($i > 0)
                                           {
                                               $out .= ', ';
                                           }
                                           
                                           $title = '';
                                           if($tag->count == 1) { $title = 'One post'; } else { $title = $tag->count.' posts'; }                                            
                                           $out .= '<a href="'.get_tag_link($tag->term_id).'" title="'.$title.'">'.$tag->name.'</a>';
                                           $i++;
                                       }                       
                                       $out .= '</div>';                                            
                                   } 
                                }
                            }                                                      
                            
                        $out .= '</div>';
                    }                       
                    
                    if($dc_post->post_excerpt != '')
                    {                               
                        $out .= apply_filters('the_content', $dc_post->post_excerpt);        
                        $out .= '<a class="bsp-more-link" href="'.$post_permalink.'" >'.__('Read more', CMS_TXT_DOMAIN).'</a>';  
                    } else
                    {                                
                        $content = strstr($dc_post->post_content, '<!--more-->', true);
                        if($content === false) { $content = $dc_post->post_content; }
                        $out .= apply_filters('the_content', $content);
                        $out .= '<a class="bsp-more-link" href="'.$post_permalink.'" >'.__('Read more', CMS_TXT_DOMAIN).'</a>';
                    }                           
                     
                $out .= '</div>';                
             
                $out .= '<div class="dc-clear-both"></div>'; 
            $out .= '</div>';         
        
        $out .= '</div>';
        
        if($echo) { echo $out; } else { return $out; }          
    }
 
    public function wpPageShort($args=array(), $echo=false)
    {
        $def = array(
            'id' => null,
            'meta' => null,
            'post' => null,
        );
        $args = $this->combineArgs($def, $args);

        if($args['post'] === null and $args['id'] !== null)
        {
            global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = ".$args['id']." AND post_status = 'publish' AND post_type = 'page' ");
            if(is_array($result))
            {
                $p = new DCC_WPPost($result[0]);
                $args['post'] = $p;
                $opt = get_post_meta($p->ID, 'page_common_opt', true);
                $args['meta'] = $opt; 
            }   
        } else                   
        if(is_object($args['post']) and $args['meta'] ===  null)
        {
            $opt = get_post_meta($args['post']->ID, 'page_common_opt', true);
            $args['meta'] = $opt;            
        }         
        
        $dc_post = $args['post'];
        $post_opt = $args['meta'];          
        
        $out = '';
        $out .= '<div class="page-short-compact-wrapper">';
            
            $date_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_display');
            $date_under_title_display = GetDCCPI()->getIGeneral()->getOption('blog_date_under_title_display'); 
            $date_format = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_format');            
            $author_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_author_display');
            $comments_display = GetDCCPI()->getIGeneral()->getOption('blog_infobar_comments_display');
            $post_permalink = get_permalink($dc_post->ID);
            $year  = mysql2date('Y', $dc_post->post_date_gmt, true);
            $month  = mysql2date('n', $dc_post->post_date_gmt, true);
            
            $out .= '<div class="bps-content">';             
            
                // image                    
                $post_image = $post_opt['page_image'];
                if(GetDCCPI()->getIGeneral()->getOption('theme_use_page_wp_thumbnail'))
                {
                    $t_url = $this->getPostThumbnailURL($dc_post->ID);
                    if($t_url !== false)
                    {
                        $post_image = $t_url;
                    }              
                }
                
                if($post_opt['page_image_hide_cbox']) { $post_image = ''; }               
                
                $is_right_side = false;   
                if($post_image != '') { $is_right_side = true; }
                   
                // right side
                if($is_right_side)
                {
                    $out .= '<div class="bps-right">';                                    
          
                        if($post_image != '')
                        {
                            $size = dcf_getImageSize($post_image);
                            
                            $out .= '<div class="bps-image-wrapper" style="max-width:'.$size['w'].'px;">';
                                $alt = $post_opt['page_image_alt'];
                                $alt = str_replace(array('"'), '', $alt); 

                                $out .= '<a class="bps-image-loader async-img-none" href="'.$post_permalink.'" rel="'.dcf_getImageURL($post_image, $size['w'], $size['h'], CMS_IMAGE_NOCROP, $post_opt['page_image_filter']).'" title="'.$alt.'"></a>';    
                            
                            $out .= '</div>';
                            
                            $image_desc = trim($post_opt['page_image_desc']);
                            if($image_desc != '' and $post_opt['page_image_desc_display_cbox'])
                            {
                                $out .= '<div class="bps-image-desc">'.$image_desc.'</div>';
                            }                        
                        }                                                
                    
                    $out .= '</div>'; 
                }   
                
                // left side
                $class = '';
                if(!$is_right_side) { $class = ' full-width'; }
                $out .= '<div class="bps-left'.$class.'">';
                    
                    // wrapper                    
                    $out .= '<div class="bps-title-wrapper">';   
                        // title            
                        $out .= '<h2 class="bps-title '.$class.'"><a href="'.$post_permalink.'">'.$dc_post->post_title.'</a></h2>';
                        
                        // posted date
                        if($date_under_title_display)
                        {
                            $out .= '<div class="bps-posted-date">';                 
                                $out .= '<a href="'.get_month_link($year, $month).'">'.__('Posted on', CMS_TXT_DOMAIN).' '.mysql2date($date_format, $dc_post->post_date_gmt).'</a>';  
                            $out .= '</div>';
                        }
                    $out .= '</div>';          
                    
                    // information bar
                    if($date_display or $author_display or $categories_display or $comments_display or $tags_display)
                    {
                        $out .= '<div class="bps-info-bar">';
                            
                            if($date_display)
                            {                       
                                $out .= '<div class="date"><a href="'.get_month_link($year, $month).'">'.mysql2date($date_format, $dc_post->post_date_gmt).'</a></div>';  
                            }
                            
                            if($comments_display and $dc_post->comment_status == 'open') 
                            {    
                                $out .= '<div class="comments">';  
                                    $text = '';
                                    if($dc_post->comment_count == 0) { $text = __('No comments', CMS_TXT_DOMAIN); } 
                                    else if($dc_post->comment_count == 1) { $text = __('One comment', CMS_TXT_DOMAIN); } 
                                    else { $text = $dc_post->comment_count.'&nbsp;'.__('comments', CMS_TXT_DOMAIN); }           
                                         
                                    $out .= '<a href="'.get_comments_link($dc_post->ID).'" class="comments">'.$dc_post->comment_count.'</a>';
                                $out .= '</div>';
                            }                              
                            
                            if($author_display)  
                            {                              
                                $out .= '<div class="author">'.__('by', CMS_TXT_DOMAIN).'&nbsp;<a href="'.get_author_posts_url($dc_post->post_author).'" class="author">'.get_the_author_meta('display_name', $dc_post->post_author).'</a>';
                                if(is_array($post_opt['page_authors']))
                                {   
                                    foreach($post_opt['page_authors'] as $author)
                                    {
                                        if($author != $dc_post->post_author)
                                        {
                                            $user_data = GetDCCPI()->getICache()->get_wp_user_by_id($author);
                                            if($user_data !== false)
                                            {
                                                $out .= ', ';
                                                $out .= '<a href="'.get_author_posts_url($user_data->ID).'" class="author">'.$user_data->display_name.'</a>';                    
                                            }
                                        }
                                    }
                                }
                                $out .= '</div>';
                            }                                                                              
                            
                        $out .= '</div>';
                    }                       
                    
                    if($dc_post->post_excerpt != '')
                    {                               
                        $out .= apply_filters('the_content', $dc_post->post_excerpt);        
                        $out .= '<a class="bsp-more-link" href="'.$post_permalink.'" >'.__('Read more', CMS_TXT_DOMAIN).'</a>';  
                    } else
                    {                                
                        $content = strstr($dc_post->post_content, '<!--more-->', true);
                        if($content === false) { $content = $dc_post->post_content; }
                        $out .= apply_filters('the_content', $content);
                        $out .= '<a class="bsp-more-link" href="'.$post_permalink.'" >'.__('Read more', CMS_TXT_DOMAIN).'</a>';
                    }                       
                    
                $out .= '</div>';                                                 
                
                $out .= '<div class="dc-clear-both"></div>';             
            $out .= '</div>';
            
        $out .= '</div>';        
                
        if($echo) { echo $out; } else { return $out; } 
    }   

    public function getHeaderContentLeftSide($echo=false)
    {
        $column = 'dc-eight';
        if(GetDCCPI()->getIGeneral()->getOption('logo_center')) { $column = 'dc-sixteen'; }
        
        $out = ''; 
        $out .= '<div class="'.$column.' dc-columns">'; 
            $out .= $this->getMainLogo();    
        $out .= '</div>';
        if($echo) { echo $out; } else { return $out; }    
    }
    
    public function getMainLogo($echo=false)
    {
        $out = '';
        
        $display = GetDCCPI()->getIGeneral()->getOption('logo_display');
        if($display)
        {        
            $w = GetDCCPI()->getIGeneral()->getOption('logo_width');
            $h = GetDCCPI()->getIGeneral()->getOption('logo_height'); 
            $h_use = GetDCCPI()->getIGeneral()->getOption('logo_height_use'); 
      
            $debug = GetDCCPI()->getIGeneral()->getOption('logo_debug_frame');
            $grayscale = GetDCCPI()->getIGeneral()->getOption('logo_grayscale'); 
            
            $img = GetDCCPI()->getIGeneral()->getOption('logo_img');
            if($grayscale)
            {
                $img = dcf_getImageURL($img, null, null, CMS_IMAGE_NOCROP, CMS_IMAGE_FILTER_GRAYSCALE, true);    
            }
            
            $linkable = GetDCCPI()->getIGeneral()->getOption('logo_linkable'); 
            $target_blank = GetDCCPI()->getIGeneral()->getOption('logo_target_blank');
            $link_to_page = GetDCCPI()->getIGeneral()->getOption('logo_link_to_page'); 
            $link_to_url = GetDCCPI()->getIGeneral()->getOption('logo_link_to_url'); 
            $link_to_theme = GetDCCPI()->getIGeneral()->getOption('logo_link_to_theme');  
            
            $logo_page = GetDCCPI()->getIGeneral()->getOption('logo_page'); 
            $logo_url = GetDCCPI()->getIGeneral()->getOption('logo_url');                          
            
            $style = '';
            $style .= 'width:'.(int)$w.'px;';
            if($h_use)
            {
                $style .= 'height:'.(int)$h.'px;';
            }        
         //   $style .= 'background-image:url('.$img.');'; 
            if($debug) { $style .= 'border:1px solid red;'; }                      
            if($style != '') { $style = ' style="'.$style.'" '; }
            
            $target = '';
            $href = '';
            $class = '';
             
            if($linkable)
            {
                if($target_blank) { $target = ' target="_blank" '; } else { $target = ' target="_self" '; } 
                
                if($link_to_page) { $href = ' href="'.get_permalink($logo_page).'" '; } 
                else
                if($link_to_url) { $href = ' href="'.$logo_url.'" '; }
                else
                if($link_to_theme) { $href = ' href="'.get_bloginfo('url').'" '; }
    
            }
            
            
            $out .= '<div class="dc-main-logo-wrapper">';
                $out .= '<a class="dc-main-logo" '.$style.' '.$href.' '.$target.'>';
                    $out .= '<img src="'.$img.'" alt="'.get_bloginfo('name').'" />';
                $out .= '</a>';
            $out .= '</div>';

        }
        
        if($echo) { echo $out; } else { return $out; }
    }
    
    
    
    
    public function getHeaderContentRightSide($echo=false)
    {
        $out = '';
     
        $column = 'dc-eight';
        if(GetDCCPI()->getIGeneral()->getOption('logo_center')) { $column = 'dc-sixteen'; }        
        
        $out .= '<div class="'.$column.' dc-columns dc-float-right">';
            $out .= '<div class="dc-icons-and-info-box-wrapper">';                
                $out .= GetDCCPI()->getIGeneral()->getHeaderInfoBox();                              
                $out .= GetDCCPI()->getIGeneral()->getMainIconsBox(); 
                $out .= $this->getHeaderSearchBox();               
            $out .= '</div>';
        $out .= '</div>';
        
        if($echo) { echo $out; } else { return $out; }        
    }
    
    public function getHeaderSearchBox($echo=false)
    {                
        $search = GetDCCPI()->getIRenderer()->getSearchQueryVar();
        
        $out = '';
        
        if(GetDCCPI()->getIGeneral()->getOption('search_header_panel_display'))
        {
            $out .= '<div class="dc-header-search-box-wrapper">';
                $out .= '<form role="search" method="get" action="'.get_bloginfo('url').'">';
                    $out .= '<input type="text" class="dc-search-control" value="'.$search.'" name="s">';
                
                    $out .= '<div class="search-btn"></div>';
                    $out .= '<div class="dc-clear-both"></div>';
                $out .= '</form>';                
            $out .= '</div>';
        }
        
        if($echo) { echo $out; } else { return $out; }
    }
   
   /* 
    public function getMainIconsBox($echo=false)
    {
        $out = '';
        
        $out .= GetDCCPI()->getIGeneral()->getMainIconsBox();
        
        if($echo) { echo $out; } else { return $out; }
    }
   */ 
    
    public function getHeaderContent($echo=true)
    {
        $out = ''; 
        
        $out .= GetDCCPI()->getIRenderer()->getHeaderContentLeftSide();
        $out .= GetDCCPI()->getIRenderer()->getHeaderContentRightSide();
        
        $out .= '<div class="dc-clear-both"></div>';        
        
        if($echo) { echo $out; } else { return $out; }
    }
 
 
    public function getStdContactForm($args=array(), $echo=false)
    {
        $def = array(
            'id' => CMS_NOT_SELECTED,
            'address' => ''
        );
        
        $args = $this->combineArgs($def, $args);
        $out = '';
        
        if($args['id'] != CMS_NOT_SELECTED)
        {                  
            $form = GetDCCPI()->getIGeneral()->getContactFormByID($args['id']);
            if($form !== null)
            {
                $out .= '<div class="dc-theme-std-contact-form">'; 
                $out .= '<input type="hidden" name="dc_email_address" value="'.$args['address'].'" /> ';                                            
                
                $have_authorization_field = false;
                $have_func_name_field = false; 
                $have_func_email_field = false;
                $have_func_subject_field = false;
                $have_func_message_field = false;
         
                foreach($form->_inputs as $ctrl)
                {
                    if($ctrl->_hide) { continue; }
                    
                    $func_class = '';
                    if($ctrl->_func_name and !$have_func_name_field) { $func_class = ' dc-x-func-name'; $have_func_name_field = true; } else
                    if($ctrl->_func_email and !$have_func_email_field) { $func_class = ' dc-x-func-email'; $have_func_email_field = true; } else
                    if($ctrl->_func_subject and !$have_func_subject_field) { $func_class = ' dc-x-func-subject'; $have_func_subject_field = true; } else
                    if($ctrl->_func_message and !$have_func_message_field) { $func_class = ' dc-x-func-message'; $have_func_message_field = true; }
                    $title = ' title="" ';
                    
                    
                    if(!$ctrl->_hide_label)
                    {         
                        $out .= '<div class="dc-control-label">';
                            $out .= '<span class="inner-label">'.$ctrl->_label.'</span>';
                            $title = ' title="'.$ctrl->_label.'" ';
                            if($ctrl->_required)
                            {
                                $out .= '<span class="inner-info">('.__('required', CMS_TXT_DOMAIN).')</span>';    
                            }
                        $out .= '</div>';
                    } else
                    {
                        $out .= '<div class="dc-control-label-empty"></div>';    
                    }                                
                    
                    if($ctrl->_is_authorization and !$have_authorization_field) 
                    {

                        
                        $secure_data = dcf_getSecurityImage();
                        $out .= '<input type="hidden" name="dc_scode" value="'.$secure_data['code'].'" /> ';
                        $out .= '<input type="text" class="dc-x-required" name="dc_scodeuser" value="" />';                  
                        $out .= '<div class="dc-authorization-image">'.$secure_data['image'].'</div>';
                        $have_authorization_field = true;              
                    } else                            
                    if($ctrl->_type == 'text')
                    {                                                                
                        $class = '';
                        if($ctrl->_required) { $class .= 'dc-x-required'; }   
                        if($ctrl->_is_email) { $class .= ' dc-x-email-analyse'; } 
                        
                        $style = '';
                        if($ctrl->_use_width) { $style .= 'width:'.$ctrl->_width.'px;'; }                                                              
                        $out .= '<input type="text" style="'.$style.'" class="'.$class.$func_class.'" value="'.($ctrl->_use_default ? $ctrl->_default : '').'" '.$title.'/>';   
                    } else
                    if($ctrl->_type == 'select')
                    {                                                                
                        $class = '';
                        if($ctrl->_required) { $class .= 'dc-x-required'; }   
                        
                        $style = '';
                        if($ctrl->_use_width) { $style .= 'width:'.$ctrl->_width.'px;'; }                                                              
                        $out .= '<select type="text" style="'.$style.'" class="'.$class.$func_class.'" '.$title.'>';
                            if(is_array($ctrl->_options))
                            {
                                foreach($ctrl->_options as $opt)
                                {
                                    $out .= '<option value="'.$opt->_value.'" '.($opt->_id == $ctrl->_default_option ? ' selected="selected" ' : '').'>'.$opt->_value.'</option>';
                                }                                 
                            }
                        $out .= '</select>';   
                    } else
                    if($ctrl->_type == 'textarea')
                    {                                                                
                        $class = '';
                        if($ctrl->_required) { $class .= 'dc-x-required'; }   
                        
                        $style = '';
                        if($ctrl->_use_width) { $style .= 'width:'.$ctrl->_width.'px;'; }                                      
                        if($ctrl->_use_height) { $style .= 'height:'.$ctrl->_height.'px;'; }
                        $out .= '<textarea type="text" style="'.$style.'" class="'.$class.$func_class.'" '.$title.'>'.($ctrl->_use_default ? $ctrl->_default : '').'</textarea>';   
                    }                                      
                }
                    $out .= '<div class="dc-submit-btn-field">';
                        $out .= '<input type="button" class="dc-submit-btn" value="'.__('Send message', CMS_TXT_DOMAIN).'" />';
                    $out .= '</div>';
                    
                    $out .= '<div class="return-info"></div>';
                $out .= '</div>';   
            }                                                      
        }    
        
        if($echo) { echo $out; } else { return $out; }    
    }
    
    public function renderBoardSlider($args=array(), $echo=false)
    {
        $const_per_line = 4;
        $const_min_lines = 1; 
        $const_max_lines = 6;
         
        $def = array(
            'lines_count' => 2,
            'pages_count' => 2,
            'cats_post' => array(),
            'cats_page' => array(),
            'list' => '',
            'in_posts' => true,
            'in_pages' => true,
            'mode' => 'normal', // normal, full-gray, full-gray-hover-color
            'excerpt' => true,
            'title' => true,
            'excerpt_words_count' => 16,
            'order' => 'DESC',
            'orderby' => 'date',
            'autoplay' => false,
            'autoplay_time' => 8,
            'desc_hide_on_hover' => false,
            'desc_show_on_hover' => false,
            'desc_bottom' => false,
            'show_hidden_post_img' => false           
        );  
        $args = self::combineArgs($def, $args);
           
        $args['lines_count'] = (int)$args['lines_count'];
        if($args['lines_count'] < $const_min_lines or $args['lines_count'] > $const_max_lines) { $args['lines_count'] = $const_min_lines; }
        
        $posts_per_page = $args['lines_count']*$const_per_line;
        
        if(!is_array($args['cats_post'])) { $args['cats_post'] = array(); }
        if(!is_array($args['cats_page'])) { $args['cats_page'] = array(); }
        
        if($args['list'] == '')
        {                
            if(count($args['cats_post']) == 0)
            {
                $c = GetDCCPI()->getICache()->get_terms_by_post_type(array('category'), array('post'));
                if(is_array($c))
                {
                    foreach($c as $cat)
                    {
                        array_push($args['cats_post'], $cat->term_id);
                    }
                }    
            }

            if(count($args['cats_page']) == 0)
            {     
                $c = GetDCCPI()->getICache()->get_terms_by_post_type(array('category_page'), array('page'));
                if(is_array($c))
                {
                    foreach($c as $cat)
                    {
                        array_push($args['cats_page'], $cat->term_id);
                    }
                }    
            }  
        }
        
        $post_type = array();
        if($args['in_posts']) { array_push($post_type, 'post'); }
        if($args['in_pages']) { array_push($post_type, 'page'); }
        
        $query_args = array(
            'paged' => 1, 
            'post_type' => $post_type, 
            'post_status' => 'publish'
        );
        
        if($args['list'] != '')
        {
            $query_args['post__in'] = explode(',', $args['list']);
        }        
        
        if($args['list'] == '')
        {               
            $query_args['tax_query'] = array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $args['cats_post'],
                    'operator' => 'IN'
                ),        
                array(
                    'taxonomy' => 'category_page',
                    'field' => 'id',
                    'terms' => $args['cats_page'],
                    'operator' => 'IN'
                )        
            );         
        }        
        
        $query_args['order'] = $args['order'];  
        $query_args['orderby'] = $args['orderby'];  
        $query_args['posts_per_page'] = $posts_per_page*$args['pages_count'];                                                               

        $dc_query = new WP_Query($query_args);  
        
        // if list, sort posts by user list order
        if($args['list'] != '')
        {
            if(is_array($dc_query->posts) and count($dc_query->posts))
            {
                $client_ids = explode(',', $args['list']);
                $spl = array();
                foreach($client_ids as $list_id)
                {
                    $list_id = (int)$list_id;
                    foreach($dc_query->posts as $p)
                    {
                        if($list_id == $p->ID)
                        {
                            array_push($spl, $p);
                            break;    
                        }
                    }
                }
                $dc_query->posts = $spl;
            }    
        }           
          
        $out = ''; 
        
        if($dc_query->post_count > 0)
        {
            
            $out .= '<div class="dc-board-slider opt-'.$args['lines_count'].'-line '.($args['pages_count'] < 2 ? ' no-navigaton' : '').'">';

                if($args['autoplay']) { $out .= '<span class="s-opt autoplay-time">'.($args['autoplay_time']*1000).'</span>'; }
                if($args['desc_show_on_hover']) { $out .= '<span class="s-opt cell-desc-on-hover">true</span>'; }  
            
                $out .= '<div class="panel">';
                    $out .= '<div class="next-btn"></div>';
                    $out .= '<div class="prev-btn"></div>';
                $out .= '</div>';
            
                $out .= '<div class="pages-container">';            

                    $post_index = 0;
                    for($page_index = 0; $page_index < $args['pages_count']; $page_index++)
                    {
                        if($post_index >= $dc_query->post_count) { break; }
                        
                        $out .= '<div class="slide-page">';
                            for($i = 0; $i < $posts_per_page; $i++)
                            {
                                if($post_index >= $dc_query->post_count) { break; }
                                
                                $p = new DCC_WPPost($dc_query->posts[$post_index]);
                                $meta = null;
                                $image_url = '';
                                $image_alt = '';  
                                
                                if($p->post_type == 'post')
                                {
                                    $meta = get_post_meta($p->ID, 'post_opt', true);     
                                    $image_url = $meta['post_image'];
                                    $image_alt = $meta['post_image_alt'];
                                   
                                    if(GetDCCPI()->getIGeneral()->getOption('theme_use_post_wp_thumbnail'))
                                    {
                                        $t_url = $this->getPostThumbnailURL($p->ID);
                                        if($t_url !== false)
                                        {
                                            $image_url = $t_url;
                                        }              
                                    }
                                    if($meta['post_image_hide_cbox'] and !$args['show_hidden_post_img']) { $image_url = ''; }                                     
                                } else
                                if($p->post_type == 'page')
                                {
                                    $meta = get_post_meta($p->ID, 'page_common_opt', true);
                                    $image_url = $meta['page_image'];
                                    $image_alt = $meta['page_image_alt'];   
                                    
                                    if(GetDCCPI()->getIGeneral()->getOption('theme_use_page_wp_thumbnail'))
                                    {   
                                        $t_url = $this->getPostThumbnailURL($p->ID);
                                        if($t_url !== false)
                                        {
                                            $image_url = $t_url;
                                        }              
                                    }                                         
                                }
                                
                                $data_desc = '';
                                if($args['desc_hide_on_hover']) { $data_desc = ' data-desc="hide" '; }
                                
                                $out .= '<div class="cell" '.$data_desc.'>';
                                
                                    $img = dcf_getImageURL($image_url, 283, 283, CMS_IMAGE_CROP_FIT);
                                    $img_gray = dcf_getImageURL($image_url, 283, 283, CMS_IMAGE_CROP_FIT, CMS_IMAGE_FILTER_GRAYSCALE);                                                                                                                                   
                                    
                                    $image_rgb = $img;
                                    $image_gray = $img_gray;
                                    $image_hover = $img;
                                    
                                    if($args['mode'] == 'full-gray')
                                    {
                                        $image_rgb = $img_gray;
                                        $image_hover = $img_gray;
                                    }
                                    
                                    if($args['mode'] == 'full-gray-hover-color')
                                    {
                                        $image_rgb = $img_gray;    
                                    }
                                    
                                    $out .= '<a href="'.get_permalink($p->ID).'" class="image-rgb async-img-none" rel="'.$image_rgb.'"></a>';                                                            
                                    $out .= '<a href="'.get_permalink($p->ID).'" class="image-gray">';
                                        if($image_url != '') { $out .= '<img src="'.$image_gray.'" alt="'.$image_alt.'" />'; }
                                    $out .= '</a>';
                                    $out .= '<div class="image-filter"></div>';
                                    $out .= '<div class="image-hover">';
                                        if($image_url != '') { $out .= '<img src="'.$image_hover.'" alt="'.$image_alt.'" />'; }
                                    $out .= '</div>';
                               
                                    
                                    if($args['title'] or ($args['excerpt'] and $p->post_excerpt != ''))
                                    {
                                        $add_class = '';
                                        if($args['desc_bottom']) { $add_class = 'bottom-pos'; }
                                        
                                        $out .= '<a href="'.get_permalink($p->ID).'" class="image-info '.$add_class.'">';
                                            if($args['title'])
                                            {
                                                $out .= $p->post_title;
                                            }
                                            if($args['excerpt'] and $p->post_excerpt != '')
                                            {
                                                $out .= '<span class="sub-text">';
                                                    $out .= dcf_strNWords($p->post_excerpt, $args['excerpt_words_count']);
                                                $out .= '</span>';
                                            }
                                        $out .= '</a>';                            
                                    }
                                    
                                    $out .= '<a href="'.get_permalink($p->ID).'" class="image-trigger"></a>';
                                
                                $out .= '</div> '; // cell
                                
                                $post_index++;                       
                            }   
                        $out .= '</div>';
                                             
                    }
                
                $out .= '</div>'; // pages-container
            $out .= '</div>';
        }          
        
        if($echo) { echo $out; } else { return $out; }     
    }
 
    public function renderServices($args=array(), $echo=false)
    {
        $def = array(
            'count' => 10,
            'cats' => array(),
            'words' => 24,
            'columns' => 2,
            'size' => 80,
            'layout' => 'classic', // classic, box 
            'list' => '',           
            'order' => 'DESC', // DESC, ASC
            'orderby' => 'date', // date, title, rand,
            'viewport_use' => false,
            'viewport_w' => 400,
            'viewport_h' => 400,
            'item_bottom' => 35,
            'item_bottom_use' => false
        );
        $args = $this->combineArgs($def, $args);
       
        if($args['list'] == '')
        {
            if(!is_array($args['cats'])) { $args['cats'] = array(); }
            if(count($args['cats']) == 0)
            {
                $cats = get_terms(DCC_ControlPanelCustomPosts::PT_SERVICE_CATEGORY, array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($cats)) 
                { 
                    $cats = array(); 
                } else 
                {
                    $temp = array();
                    foreach($cats as $cat)
                    {
                        array_push($temp, $cat->term_id);
                    }
                    $cats = $temp;
                }
                $args['cats'] = $cats;        
            }
        }                
        
        $query_args = array(
            'posts_per_page' => $args['count'], 
            'paged' => 1, 
            'nopaging' => false, 
            'post_status' => 'publish', 
            'ignore_sticky_posts' => false, 
            'post_type' => DCC_ControlPanelCustomPosts::PT_SERVICE_POST,
            'order' => $args['order'],
            'orderby' => $args['orderby']
        );
        
        if($args['list'] != '')
        {
            $query_args['post__in'] = explode(',', $args['list']);
        }
  
        if($args['list'] == '')
        {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => DCC_ControlPanelCustomPosts::PT_SERVICE_CATEGORY,
                    'field' => 'id',
                    'terms' => $args['cats'],
                    'operator' => 'IN'
                )            
            );
        }  
             
        $dc_query = new WP_Query($query_args); 

        // if list, sort posts by user list order
        if($args['list'] != '')
        {
            if(is_array($dc_query->posts) and count($dc_query->posts))
            {
                $client_ids = explode(',', $args['list']);
                $spl = array();
                foreach($client_ids as $list_id)
                {
                    $list_id = (int)$list_id;
                    foreach($dc_query->posts as $p)
                    {
                        if($list_id == $p->ID)
                        {
                            array_push($spl, $p);
                            break;    
                        }
                    }
                }
                $dc_query->posts = $spl;
            }    
        }          
        
        if($args['columns'] != 1 and $args['columns'] != 2 and 
           $args['columns'] != 3 and $args['columns'] != 4) { $args['columns'] = 1; }              
        
        $out = '';
        $out .= '<div class="dc-service-seg-wrapper">';
            for($i = 0; $i < $dc_query->post_count; $i++)
            {            
                $service = new DCC_WPPost($dc_query->posts[$i]);
                $meta = get_post_meta($service->ID, 'dc_service_opt', true);
                $permalink = get_permalink($service->ID);
                $is_link = $meta['dc_service_display_link_cbox'];
                if($meta['dc_service_link_to_page_cbox'] and $meta['dc_service_page'] != CMS_NOT_SELECTED)
                {
                    $permalink = get_permalink($meta['dc_service_page']);    
                }
                
                if($i % $args['columns'] == 0) { $out .= '<div class="dc-clear-both"></div>'; }
                
                $last_class = '';
                if($args['columns'] > 1)
                {
                    if(($i % $args['columns']) == ($args['columns']-1)) { $last_class = '-last'; }
                }
                
                $last_item_class = '';
                if($i == ($dc_query->post_count-1))              
                {
                    $last_item_class = ' last-item-in-seg';
                }

                $last_line_start_index = dcf_getLastLineIndex($dc_query->post_count, $args['columns']);                              
                $last_line_class = '';                
                if($i >= $last_line_start_index)              
                {
                    $last_line_class = ' last-line-in-seg';
                }
                
                $style = '';
                    if($args['item_bottom_use']) { $style .= 'margin-bottom:'.$args['item_bottom'].'px;'; }                    
                $style = ' style="'.$style.'" ';                
                
                if($args['layout'] == 'classic')
                {            
                    $out .= '<div class="dc-service-item-1-'.$args['columns'].$last_class.$last_item_class.$last_line_class.'" '.$style.'>';
                        $out .= '<div class="left-side" style="width:'.$args['size'].'px;">';
                            if($is_link) { $out .= '<a href="'.$permalink.'">'; }  
                                $out .= '<img src="'.$meta['dc_service_image'].'" alt="Service" />';
                            if($is_link) { $out .= '</a>'; }  
                        $out .= '</div>';    
                        
                        $out .= '<div class="right-side" style="margin-left:'.$args['size'].'px;">';
                            $out .= '<h4>';
                                if($is_link) { $out .= '<a href="'.$permalink.'">'; }
                                    $out .= $service->post_title;
                                    if($meta['dc_service_subtitle_display_cbox'] and $meta['dc_service_subtitle'] != '')
                                    {
                                        $out .= '<span>'.$meta['dc_service_subtitle'].'</span>';
                                    }
                                if($is_link) { $out .= '</a>'; }
                            $out .= '</h4>';
                            if($service->post_excerpt != '')
                            {
                                $out .= '<div class="text-content">';
                                    $out .= dcf_strNWords($service->post_excerpt, $args['words']);
                                $out .= '</div>';
                            } else
                            {
                                $out .= '<div class="text-content">';
                                    $out .= dcf_strNWords($service->post_content, $args['words']);
                                $out .= '</div>';                        
                            }
                            if($is_link) 
                            {
                                $out .= '<div class="more-link-wrapper">';
                                    $out .= '<a href="'.$permalink.'" class="more-link">'.__('Read more', CMS_TXT_DOMAIN).'</a>';
                                $out .= '</div>';  
                            }                  
                        $out .= '</div>';
                        
                        $out .= '<div class="dc-clear-both"></div>';
                    $out .= '</div>';             
                } else
                {
                    $out .= '<div class="dc-service-item-1-'.$args['columns'].'-box'.$last_class.$last_item_class.$last_line_class.'" '.$style.'>';
                        $out .= '<div class="top-side">';
                            if($is_link) { $out .= '<a href="'.$permalink.'">'; } 
                                if($args['viewport_use'])
                                {
                                    $out .= '<img src="'.dcf_getImageURL($meta['dc_service_image'], $args['viewport_w'], $args['viewport_h']).'" alt="Service" />';    
                                } else
                                {
                                    $out .= '<img src="'.$meta['dc_service_image'].'" alt="Service" />';
                                }
                            if($is_link) { $out .= '</a>'; }  
                        $out .= '</div>';    
                        
                        $out .= '<div class="bottom-side">';
                            $out .= '<h4>';
                                if($is_link) { $out .= '<a href="'.$permalink.'">'; } 
                                    $out .= $service->post_title;
                                    if($meta['dc_service_subtitle'] != '')
                                    {
                                        $out .= '<span>'.$meta['dc_service_subtitle'].'</span>';
                                    }
                                if($is_link) { $out .= '</a>'; } 
                            $out .= '</h4>';
                            if($service->post_excerpt != '')
                            {
                                $out .= '<div class="text-content">';
                                    $out .= dcf_strNWords($service->post_excerpt, $args['words']);
                                $out .= '</div>';
                            } else
                            {
                                $out .= '<div class="text-content">';
                                    $out .= dcf_strNWords($service->post_content, $args['words']);
                                $out .= '</div>';                        
                            }
                            if($is_link) 
                            {                        
                                $out .= '<div class="more-link-wrapper">';
                                    $out .= '<a href="'.$permalink.'" class="more-link">'.__('Read more', CMS_TXT_DOMAIN).'</a>';
                                $out .= '</div>';   
                            }                 
                        $out .= '</div>';
                        
                        $out .= '<div class="dc-clear-both"></div>';
                    $out .= '</div>';                  
                }                           
            } 
            $out .= '<div class="dc-clear-both"></div>';   
        $out .= '</div>';
     
        if($echo) { echo $out; } else { return $out; }
    }
 
    public function getServiceLayoutClass($args=array())
    {
        global $dc_service_opt;
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;
        
        if($dc_service_opt !== false) { $layout = $dc_service_opt['dc_service_layout']; }
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    

        return $class;
    }   
 
    public function wpServiceLeftSidebar($args=array(), $echo=true)
    {
        global $dc_service_opt;
        $out = '';
        
        if($dc_service_opt['dc_service_layout'] == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $dc_service_opt['dc_service_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_service_opt['dc_service_sid_left']; }                   
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_left'); }
            if($args['layout'] === null) { $args['layout'] = $dc_service_opt['dc_service_layout']; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }

    public function wpServiceRightSidebar($args=array(), $echo=true)
    {
        global $dc_service_opt;
        $out = '';
        
        if($dc_service_opt['dc_service_layout'] == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $dc_service_opt['dc_service_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_service_opt['dc_service_sid_right']; }
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_right'); }                
            if($args['layout'] === null) { $args['layout'] = $dc_service_opt['dc_service_layout']; }                                           
        
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']); 
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    } 
 
    public function wpServiceCustomOptCSS($echo=true)
    {   
        global $dc_is_single;
        global $dc_service_opt;
        global $post;
        $out = '';
                      
        if($dc_is_single and $post->post_type == DCC_ControlPanelCustomPosts::PT_SERVICE_POST)
        {                                                 
            if($dc_service_opt !== false)
            {   
                $pct = & $dc_service_opt; 
                
                $out .= ' <style type="text/css">';

                if(!GetDCCPI()->getIGeneral()->getOption('bg_force'))
                {                                                    
                    if($pct['dc_service_bg_use_cbox'] or (bool)$pct['dc_service_bg_color_use_cbox'])
                    {   
                        $out .= ' body { ';
                            if($pct['dc_service_bg_use_cbox'])
                            {
                                $out .= 'background-image:url('.$pct['dc_service_bg_image'].');';
                                $out .= 'background-repeat:'.$pct['dc_service_bg_repeat'].';';
                                $out .= 'background-attachment:'.$pct['dc_service_bg_attachment'].';';
                                
                                $pos_x = $pct['dc_service_bg_pos_x'];
                                $pos_y = $pct['dc_service_bg_pos_y'];
                                if($pct['dc_service_bg_pos_x_px_use_cbox']) { $pos_x = $pct['dc_service_bg_pos_x_px'].'px'; }
                                if($pct['dc_service_bg_pos_y_px_use_cbox']) { $pos_y = $pct['dc_service_bg_pos_y_px'].'px'; }
                                
                                $out .= 'background-position:'.$pos_x.' '.$pos_y.';';
                            }
                            
                            if($pct['dc_service_bg_color_use_cbox'])
                            {
                                $out .= 'background-color:'.$pct['dc_service_bg_color'].';';    
                            } 
                        $out .= ' } ';        
                    }
                }
                $out .= ' </style> ';
            }
        
        }
        
        if($echo) { echo $out; } else { return $out; }
    }   
 
    public function getProjectLayoutClass($args=array())
    {
        global $dc_project_opt;
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;
        
        if($dc_project_opt !== false) { $layout = $dc_project_opt['dc_project_layout']; }
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    

        return $class;
    }   
 
    public function wpProjectLeftSidebar($args=array(), $echo=true)
    {
        global $dc_project_opt;
        $out = '';
        
        if($dc_project_opt['dc_project_layout'] == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $dc_project_opt['dc_project_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_project_opt['dc_project_sid_left']; }                   
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_left'); }
            if($args['layout'] === null) { $args['layout'] = $dc_project_opt['dc_project_layout']; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }

    public function wpProjectRightSidebar($args=array(), $echo=true)
    {
        global $dc_project_opt;
        $out = '';
        
        if($dc_project_opt['dc_project_layout'] == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $dc_project_opt['dc_project_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_project_opt['dc_project_sid_right']; }
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_right'); }                
            if($args['layout'] === null) { $args['layout'] = $dc_project_opt['dc_project_layout']; }                                           
        
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']); 
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }  
 
    public function wpProjectCustomOptCSS($echo=true)
    {   
        global $dc_is_single;
        global $dc_project_opt;
        global $post;
        $out = '';
                      
        if($dc_is_single and $post->post_type == DCC_ControlPanelCustomPosts::PT_PROJECT_POST)
        {                                                 
            if($dc_project_opt !== false)
            {   
                $pct = & $dc_project_opt; 
                
                $out .= ' <style type="text/css">';

                if(!GetDCCPI()->getIGeneral()->getOption('bg_force'))
                {                                                    
                    if($pct['dc_project_bg_use_cbox'] or (bool)$pct['dc_project_bg_color_use_cbox'])
                    {   
                        $out .= ' body { ';
                            if($pct['dc_project_bg_use_cbox'])
                            {
                                $out .= 'background-image:url('.$pct['dc_project_bg_image'].');';
                                $out .= 'background-repeat:'.$pct['dc_project_bg_repeat'].';';
                                $out .= 'background-attachment:'.$pct['dc_project_bg_attachment'].';';
                                
                                $pos_x = $pct['dc_project_bg_pos_x'];
                                $pos_y = $pct['dc_project_bg_pos_y'];
                                if($pct['dc_project_bg_pos_x_px_use_cbox']) { $pos_x = $pct['dc_project_bg_pos_x_px'].'px'; }
                                if($pct['dc_project_bg_pos_y_px_use_cbox']) { $pos_y = $pct['dc_project_bg_pos_y_px'].'px'; }
                                
                                $out .= 'background-position:'.$pos_x.' '.$pos_y.';';
                            }
                            
                            if($pct['dc_project_bg_color_use_cbox'])
                            {
                                $out .= 'background-color:'.$pct['dc_project_bg_color'].';';    
                            } 
                        $out .= ' } ';        
                    }
                }
                $out .= ' </style> ';
            }
        
        }
        
        if($echo) { echo $out; } else { return $out; }
    }   
 
    public function wpProjectFull($echo=true) 
    {
        global $dc_post;
        global $dc_project_opt;
        global $page, $pages, $multipage, $numpages;         

        $out = '';
        
        if($dc_project_opt['dc_project_only_content_cbox'])
        {
            $out .= apply_filters('the_content', $dc_post->post_content);             
        } else
        {            
            $out .= '<div class="project-post-full-wrapper">';
            
                $post_permalink = get_permalink($dc_post->ID);
                $date_format = GetDCCPI()->getIGeneral()->getOption('project_infobar_date_format'); 
                $posted_date_display = GetDCCPI()->getIGeneral()->getOption('project_single_posted_date');
                $date_display = GetDCCPI()->getIGeneral()->getOption('project_infobar_date_display');         
                $author_display = GetDCCPI()->getIGeneral()->getOption('project_infobar_author_display');
                $categories_display = GetDCCPI()->getIGeneral()->getOption('project_infobar_categories_display');
                $comments_display = GetDCCPI()->getIGeneral()->getOption('project_infobar_comments_display');                
                $skills_display = GetDCCPI()->getIGeneral()->getOption('project_infobar_skills_display');
                $client_display = GetDCCPI()->getIGeneral()->getOption('project_infobar_client_display');
                $website_display = GetDCCPI()->getIGeneral()->getOption('project_infobar_website_display');
                $year  = mysql2date('Y', $dc_post->post_date_gmt, true);
                $month  = mysql2date('n', $dc_post->post_date_gmt, true);
            
                // title
                $class = '';
                
                $out .= '<div class="ppf-title-wrapper">';                
                    $out .= '<h1 class="ppf-title">'.$dc_post->post_title.'</h1>';
                    
                    // posted date
                    if($posted_date_display)
                    {
                        $out .= '<div class="ppf-posted-date">';                 
                            $out .= __('Posted on', CMS_TXT_DOMAIN).' '.mysql2date($date_format, $dc_post->post_date_gmt);  
                        $out .= '</div>';              
                    }                
                $out .= '</div>';
                
                // image
                $post_image = $dc_project_opt['dc_project_image'];            
                  
                $post_video_url = $dc_project_opt['dc_project_video_url']; 
                $is_vimeo = strstr($post_video_url, 'vimeo.com') !== false ? true : false;
                $is_youtube = strstr($post_video_url, 'youtube.com') !== false ? true : false;

                if(($is_vimeo or $is_youtube) and $dc_project_opt['dc_project_video_display_cbox'])
                {    
                    $out .= '<div class="ppf-video-wrapper">';
                        $time = time();
                        if($is_vimeo)
                        {
                            $pos = strrpos($post_video_url, '/') + 1;
                            $video_id = substr($post_video_url, $pos);
                            $out .= '<iframe src="http://player.vimeo.com/video/'.$video_id.'?dc_param='.$time.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                        } else
                        if($is_youtube)
                        {
                            $url = parse_url($post_video_url);
                            $vars = array();
                            parse_str($url['query'], $vars);
                            $out .= '<iframe src="http://www.youtube.com/embed/'.$vars['v'].'?rel=0&wmode=transparent" frameborder="0" allowfullscreen></iframe>';
                        }                                       
                    $out .= '</div>';
                    if($dc_project_opt['dc_project_video_desc'] != '' and $dc_project_opt['dc_project_video_desc_display_cbox'])
                    {
                        $out .= '<div class="ppf-video-description">'.$dc_project_opt['dc_project_video_desc'].'</div>';    
                    }                          
                } else                      
                if($post_image != '')
                {
                    $size = dcf_getImageSize($post_image);
                    
                    $out .= '<div class="ppf-image-wrapper" style="max-width:'.$size['w'].'px;">';                         
                        $alt = $dc_project_opt['dc_project_image_alt'];
                        $alt = str_replace(array('"'), '', $alt);
                                      
                        $out .= '<a class="ppf-image-loader async-img-none" rel="'.dcf_getImageURL($post_image, $size['w'], $size['h'], CMS_IMAGE_NOCROP, $dc_project_opt['dc_project_image_filter']).'" title="'.$alt.'"></a>';    
                                   
                        $image_desc = trim($dc_project_opt['dc_project_image_desc']); 
                        if($image_desc != '' and $dc_project_opt['dc_project_image_desc_display_cbox'])
                        {
                            $out .= '<span class="ppf-image-desc">'.$image_desc.'</span>';
                        }
                    $out .= '</div>';
                }   
               
                // work information                               
                $out .= '<div class="ppf-work-information-bar">';
                
                    $out .= '<div class="right-side">';
                        $next = dcf_nextPost(DCC_ControlPanelCustomPosts::PT_PROJECT_POST);
                        if($next !== false) { $out .= '<a href="'.get_permalink($next->ID).'" class="prev active" title="'.__('Newer', CMS_TXT_DOMAIN).'"></a>'; } 
                        else { $out .= '<a class="prev no-prev"></a>'; }

                        $prev = dcf_prevPost(DCC_ControlPanelCustomPosts::PT_PROJECT_POST);
                        if($prev !== false) { $out .= '<a href="'.get_permalink($prev->ID).'" class="next active" title="'.__('Older', CMS_TXT_DOMAIN).'"></a>'; } 
                        else { $out .= '<a class="next no-next"></a>'; }                        
                    $out .= '</div>';                    

                    $out .= '<div class="left-side">';
                        if($client_display and $dc_project_opt['dc_project_about_client_name'] != '')
                        {
                            if($dc_project_opt['dc_project_about_client_url'] != '')
                            {
                                $out .= '<div class="client"><a href="'.$dc_project_opt['dc_project_about_client_url'].'" target="_blank">'.$dc_project_opt['dc_project_about_client_name'].'</a></div>'; 
                            } else
                            {
                                $out .= '<div class="client">'.$dc_project_opt['dc_project_about_client_name'].'</div>'; 
                            }
                        }
                        if($website_display and $dc_project_opt['dc_project_about_website_name'] != '')
                        {
                            if($dc_project_opt['dc_project_about_website_url'] != '')
                            {
                                $out .= '<div class="website"><a href="'.$dc_project_opt['dc_project_about_website_url'].'" target="_blank">'.$dc_project_opt['dc_project_about_website_name'].'</a></div>'; 
                            } else
                            {
                                $out .= '<div class="website">'.$dc_project_opt['dc_project_about_website_name'].'</div>'; 
                            }
                        }
                        if($skills_display and $dc_project_opt['dc_project_about_skills'] != '')
                        {
                            $out .= '<div class="skills">'.$dc_project_opt['dc_project_about_skills'].'</div>'; 
                        }
                        
                        if($date_display)
                        {                       
                            $out .= '<div class="date">'.mysql2date($date_format, $dc_post->post_date_gmt).'</div>';  
                        }     
                        
                        if($comments_display and $dc_post->comment_status == 'open') 
                        {    
                            $out .= '<div class="comments">';  
                                $text = '';
                                if($dc_post->comment_count == 0) { $text = __('No comments', CMS_TXT_DOMAIN); } 
                                else if($dc_post->comment_count == 1) { $text = __('One comment', CMS_TXT_DOMAIN); } 
                                else { $text = $dc_post->comment_count.'&nbsp;'.__('comments', CMS_TXT_DOMAIN); }           
                                     
                                $out .= '<a href="'.get_comments_link($dc_post->ID).'" class="comments">'.$dc_post->comment_count.'</a>';
                            $out .= '</div>';
                        }                              
                        
                        if($author_display)  
                        {                              
                            $out .= '<div class="author">'.__('by', CMS_TXT_DOMAIN).'&nbsp;'.get_the_author_meta('display_name', $dc_post->post_author);
                            $out .= '</div>';
                        }
                        
                        if($categories_display)
                        {    
                            $catlist = wp_get_object_terms($dc_post->ID, DCC_ControlPanelCustomPosts::PT_PROJECT_CATEGORY);
                            $count = count($catlist);
                            if($dc_post > 0)
                            {
                                $out .= '<div class="categories">';
                                    for($i = 0; $i < $count; $i++)
                                    {
                                        if($i > 0) { $out .= ', '; }
                                        $cat = get_category($catlist[$i]);
                                        $out .= '<a href="'.get_category_link($catlist[$i]).'" >'.$cat->name.'</a>';
                                         
                                    }
                                $out .= '</div>';
                            }                  
                        }   
                                                                                                         
                    $out .= '</div>';
                    $out .= '<div class="dc-clear-both"></div>'; 
                $out .= '</div>';            
                
                // content
                $out .= '<div class="ppf-content">'; 

                    if($multipage)
                    {  
                        $out .= apply_filters('the_content', $pages[$page-1]);            
                    } else
                    {
                        $out .= apply_filters('the_content', $dc_post->post_content);                       
                    }   
                                   
                    $out .= '<div class="dc-clear"></div>'; 
                $out .= '</div>'; 
                $out .= GetDCCPI()->getIRenderer()->wpPaginationBlock(false);                         
                
            $out .= '</div>';  
        }      
        
        if($echo) { echo $out; } else { return $out; }    
    }
 
    public function renderProjects($args=array(), $echo=false)
    {
        $def = array(
            'per_page' => 8,
            'cats' => array(),
            'words' => 24,
            'list' => '',           
            'columns' => 1,
            'order' => 'DESC', // DESC, ASC
            'orderby' => 'date', // date, title, comment_count
            'pagination' => false,
            'excerpt' => true,
            'viewport_w' => 300,
            'viewport_h' => 400,
            'viewport_use' => false,
            'cats_display' => true,
            'title_display' => true,
            'item_bottom' => 30,
            'item_bottom_use' => false
        );
        $args = $this->combineArgs($def, $args);
        
        $terms = array();
        if($args['list'] == '')
        {
            if(!is_array($args['cats'])) { $args['cats'] = array(); }
            if(count($args['cats']) == 0)
            {
                $terms = get_terms(DCC_ControlPanelCustomPosts::PT_PROJECT_CATEGORY, array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                } else 
                {
                    $temp = array();
                    foreach($terms as $cat)
                    {
                        array_push($temp, $cat->term_id);
                    }
                    $args['cats'] = $temp;
                }                        
            } else
            {
                $terms = get_terms(DCC_ControlPanelCustomPosts::PT_PROJECT_CATEGORY, array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                } else
                {
                    $temp = array();
                    foreach($terms as $t)
                    {                                            
                        if(in_array($t->term_id, $args['cats']))
                        {
                            array_push($temp, $t);    
                        }                        
                    }
                    $terms = $temp;
                }    
            }
        }                
        
        $paged = $this->getPagedQueryVar();
        if(!$args['pagination']) { $paged = 1; }
        
        $query_args = array(
            'posts_per_page' => $args['per_page'], 
            'paged' => $paged, 
            'nopaging' => false, 
            'post_status' => 'publish', 
            'ignore_sticky_posts' => false, 
            'post_type' => DCC_ControlPanelCustomPosts::PT_PROJECT_POST,
            'order' => $args['order'],
            'orderby' => $args['orderby']
        );
        
        if($args['list'] != '')
        {
            $query_args['post__in'] = explode(',', $args['list']);
        }
  
        if($args['list'] == '')
        {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => DCC_ControlPanelCustomPosts::PT_PROJECT_CATEGORY,
                    'field' => 'id',
                    'terms' => $args['cats'],
                    'operator' => 'IN'
                )            
            );
        }  
             
        $dc_query = new WP_Query($query_args);  
        
        if($args['columns'] != 1 and $args['columns'] != 2 and 
           $args['columns'] != 3 and $args['columns'] != 4) { $args['columns'] = 1; }                      
        
        $out = ''; 
        $out .= '<div class="dc-project-items-wrapper">';
        
            for($i = 0; $i < $dc_query->post_count; $i++)
            {            
                $project = new DCC_WPPost($dc_query->posts[$i]);
                $meta = get_post_meta($project->ID, 'dc_project_opt', true);
                $permalink = get_permalink($project->ID);        
                $is_link = true; 
            
                if($i % $args['columns'] == 0) { $out .= '<div class="dc-clear-both"></div>'; }              
            
                $last_class = '';
                if($args['columns'] > 1)
                {
                    if(($i % $args['columns']) == ($args['columns']-1)) { $last_class = '-last'; }
                }
                
                $last_item_class = '';
                if($i == ($dc_query->post_count-1))              
                {
                    $last_item_class = ' last-item-in-seg';
                }  
                
                $last_line_start_index = dcf_getLastLineIndex($dc_query->post_count, $args['columns']);                              
                $last_line_class = '';                
                if($i >= $last_line_start_index)              
                {
                    $last_line_class = ' last-line-in-seg';
                }                              
            
                $style = '';
                    if($args['item_bottom_use']) { $style .= 'margin-bottom:'.$args['item_bottom'].'px;'; }                    
                $style = ' style="'.$style.'" ';
            
                $out .= '<div class="dc-project-item dc-project-item-1-'.$args['columns'].$last_class.$last_item_class.$last_line_class.'" '.$style.'>';
                
                        $out .= '<div class="top-side">';
                            $alt = $meta['dc_project_image_alt'];
                            $alt = str_replace(array('"'), '', $alt);
                        
                            if($is_link) { $out .= '<a href="'.$permalink.'">'; }  
                                if($args['viewport_use'])
                                {
                                    $out .= '<img src="'.dcf_getImageURL($meta['dc_project_image'], $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $meta['dc_project_image_filter']).'" alt="'.$alt.'" />';    
                                } else
                                {
                                    $out .= '<img src="'.dcf_getImageURL($meta['dc_project_image'], 0, 0, CMS_IMAGE_NOCROP, $meta['dc_project_image_filter'], true).'" alt="'.$alt.'" />';
                                }
                            if($is_link) { $out .= '</a>'; } 
                        $out .= '</div>';
                            
                        if($args['title_display'])
                        {
                            $out .= '<a class="title" href="'.$permalink.'">'.$project->post_title.'</a>'; 
                        }
                        
                        if($args['cats_display'])
                        {
                            $terms = get_the_terms($project->ID, DCC_ControlPanelCustomPosts::PT_PROJECT_CATEGORY);
                            if(is_array($terms))
                            {
                                if(count($terms))
                                {
                                    $out .= '<div class="categories-list">';
                                        $term_counter = 0;
                                        foreach($terms as $cat)
                                        {
                                            if($term_counter > 0) { $out .= ', '; }
                                            $out .= '<a href="'.get_term_link($cat->slug, DCC_ControlPanelCustomPosts::PT_PROJECT_CATEGORY).'">'.$cat->name.'</a>';
                                            $term_counter++;
                                        }
                                    $out .= '</div>';
                                }
                            }
                        }
                        
                        if($args['excerpt'] and $project->post_excerpt != '')
                        {
                            $out .= '<div class="project-excerpt">';
                                $out .= dcf_strNWords($project->post_excerpt, $args['words']);
                            $out .= '</div>';    
                        }                     
                     
                $out .= '</div>';
                
            }
            $out .= '<div class="dc-clear-both"></div>'; 
        $out .= '</div>'; 
        
        if($args['pagination'])
        {
            $out .= $this->wpQueryPaginationBlock(array('paged' => $paged, 'maxpage' => $dc_query->max_num_pages, 'top' => 10, 'pb' => 0));
        } 
        
        if($echo) { echo $out; } else { return $out; }              
    }
    
    public function wpProjectCategoryLeftSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('project_layout_category_page'); 
        if($layout == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null, 'slug' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('project_sid_default_cats_left'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd(); 
        }
        
        if($echo) { echo $out; } else { return $out; }           
    }

    public function wpProjectCategoryRightSidebar($args=array(), $echo=true)
    {
        $out = '';
        
        $layout = GetDCCPI()->getIGeneral()->getOption('project_layout_category_page'); 
        if($layout == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $layout == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null, 'slug' => null);
            $args = $this->combineArgs($def, $args);
   
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('project_sid_default_cats_right'); }                   
            if($args['layout'] === null) { $args['layout'] = $layout; }                                                            
         
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd(); 
        }
        
        if($echo) { echo $out; } else { return $out; }            
    }     
 
    public function getMemberLayoutClass($args=array())
    {
        global $dc_member_opt;
        $class = '';      
        $def = array('layout' => null);
        $args = $this->combineArgs($def, $args);
        $layout = null;
        
        if($dc_member_opt !== false) { $layout = $dc_member_opt['dc_member_layout']; }
        if($args['layout'] !== null) { $layout = $args['layout']; }
                       
        switch($layout)
        {
            case CMS_PAGE_LAYOUT_LEFT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;    
            
            case CMS_PAGE_LAYOUT_RIGHT_SIDEBAR:
                $class = 'dc-layout-one-sidebar';
            break;  
            
            case CMS_PAGE_LAYOUT_BOTH_SIDEBARS:
                $class = 'dc-layout-both-sidebar';
            break;                  

            case CMS_PAGE_LAYOUT_FULL_WIDTH:
                $class = 'dc-layout-full-width';
            break;               
        }    

        return $class;
    }   
 
    public function wpMemberLeftSidebar($args=array(), $echo=true)
    {
        global $dc_member_opt;
        $out = '';
        
        if($dc_member_opt['dc_member_layout'] == CMS_PAGE_LAYOUT_LEFT_SIDEBAR or $dc_member_opt['dc_member_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_member_opt['dc_member_sid_left']; }                   
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_left'); }
            if($args['layout'] === null) { $args['layout'] = $dc_member_opt['dc_member_layout']; }                                                            
        
            $args['side'] = CMS_SIDEBAR_LEFT;
            $out .= $this->getSidColWrapperClassStart($args['layout']);
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }

    public function wpMemberRightSidebar($args=array(), $echo=true)
    {
        global $dc_member_opt;
        $out = '';
        
        if($dc_member_opt['dc_member_layout'] == CMS_PAGE_LAYOUT_RIGHT_SIDEBAR or $dc_member_opt['dc_member_layout'] == CMS_PAGE_LAYOUT_BOTH_SIDEBARS)
        {
            $def = array('id' => CMS_NOT_SELECTED, 'layout' => null);
            $args = $this->combineArgs($def, $args);
        
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = $dc_member_opt['dc_member_sid_right']; }
            if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = GetDCCPI()->getIGeneral()->getOption('sid_default_post_right'); }                
            if($args['layout'] === null) { $args['layout'] = $dc_member_opt['dc_member_layout']; }                                           
        
            $args['side'] = CMS_SIDEBAR_RIGHT;
            $out .= $this->getSidColWrapperClassStart($args['layout']); 
                $out .= GetDCCPI()->getIGeneral()->getSidebar($args, false);
            $out .= $this->getSidColWrapperClassEnd();
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }   
    
    public function wpMemberCustomOptCSS($echo=true)
    {   
        global $dc_is_single;
        global $dc_member_opt;
        global $post;
        $out = '';
                      
        if($dc_is_single and $post->post_type == DCC_ControlPanelCustomPosts::PT_MEMBER_POST)
        {                                                 
            if($dc_member_opt !== false)
            {   
                $pct = & $dc_member_opt; 
                
                $out .= ' <style type="text/css">';

                if(!GetDCCPI()->getIGeneral()->getOption('bg_force'))
                {                                                    
                    if($pct['dc_member_bg_use_cbox'] or (bool)$pct['dc_member_bg_color_use_cbox'])
                    {   
                        $out .= ' body { ';
                            if($pct['dc_member_bg_use_cbox'])
                            {
                                $out .= 'background-image:url('.$pct['dc_member_bg_image'].');';
                                $out .= 'background-repeat:'.$pct['dc_member_bg_repeat'].';';
                                $out .= 'background-attachment:'.$pct['dc_member_bg_attachment'].';';
                                
                                $pos_x = $pct['dc_member_bg_pos_x'];
                                $pos_y = $pct['dc_member_bg_pos_y'];
                                if($pct['dc_member_bg_pos_x_px_use_cbox']) { $pos_x = $pct['dc_member_bg_pos_x_px'].'px'; }
                                if($pct['dc_member_bg_pos_y_px_use_cbox']) { $pos_y = $pct['dc_member_bg_pos_y_px'].'px'; }
                                
                                $out .= 'background-position:'.$pos_x.' '.$pos_y.';';
                            }
                            
                            if($pct['dc_member_bg_color_use_cbox'])
                            {
                                $out .= 'background-color:'.$pct['dc_member_bg_color'].';';    
                            } 
                        $out .= ' } ';        
                    }
                }
                $out .= ' </style> ';
            }
        
        }
        
        if($echo) { echo $out; } else { return $out; }
    }     
    
    public function wpMemberFull($echo=true) 
    {
        global $dc_post;
        global $dc_member_opt;
        global $page, $pages, $multipage, $numpages;         

        $out = '';
        
        if($dc_member_opt['dc_member_only_content_cbox'])
        {
            $out .= apply_filters('the_content', $dc_post->post_content);             
        } else
        {
            $out .= '<div class="member-post-full-wrapper">';              
            
                // image
                $post_image = $dc_member_opt['dc_member_image'];                              
                   
                if($post_image != '')
                {
                    $size = dcf_getImageSize($post_image);
                    
                    $out .= '<div class="ppf-image-wrapper" style="max-width:'.$size['w'].'px;">';
                        $image_desc = trim($dc_member_opt['dc_project_image_desc']);  
                        $alt = $dc_member_opt['dc_member_image_alt'];
                        $alt = str_replace(array('"'), '', $alt);
                                      
                        $out .= '<a class="ppf-image-loader async-img-none" rel="'.dcf_getImageURL($post_image, $size['w'], $size['h'], CMS_IMAGE_NOCROP, $dc_member_opt['dc_member_image_filter']).'" title="'.$alt.'"></a>';    
                                   
                        if($dc_member_opt['dc_member_image_desc'] != '' and $dc_member_opt['dc_member_image_desc_display_cbox'])
                        {
                            $out .= '<span class="ppf-image-desc">'.$dc_member_opt['dc_member_image_desc'].'</span>';
                        }
                    $out .= '</div>';
                }              
                
                // title
                $out .= '<div class="ppf-title-wrapper">';                
                    $out .= '<h1 class="ppf-title">'.$dc_post->post_title.'</h1>';           
                $out .= '</div>';
                
                if($dc_member_opt['dc_member_pi_title_cbox'] and $dc_member_opt['dc_member_pi_title'] != '')
                {
                    $out .= '<div class="ppf-pi-title">';                
                        $out .= $dc_member_opt['dc_member_pi_title'];           
                    $out .= '</div>';                    
                }                

                if($dc_member_opt['dc_member_pi_subtitle_cbox'] and $dc_member_opt['dc_member_pi_subtitle'] != '')
                {
                    $out .= '<div class="ppf-pi-subtitle">';                
                        $out .= $dc_member_opt['dc_member_pi_subtitle'];           
                    $out .= '</div>';                    
                }        
                
                if($dc_member_opt['dc_member_pi_addinfo_cbox'] and $dc_member_opt['dc_member_pi_addinfo'] != '')
                {
                    $out .= '<div class="ppf-pi-addinfo">';                
                        $out .= $dc_member_opt['dc_member_pi_addinfo'];           
                    $out .= '</div>';                    
                }                     
                      
                if(($dc_member_opt['dc_member_pi_twitter_cbox'] and $dc_member_opt['dc_member_pi_twitter'] != '') or 
                   ($dc_member_opt['dc_member_pi_facebook_cbox'] and $dc_member_opt['dc_member_pi_facebook'] != '') or
                   ($dc_member_opt['dc_member_pi_website_cbox'] and $dc_member_opt['dc_member_pi_website'] != ''))
                {
                    $out .= '<div class="ppf-pi-links">'; 
                        if($dc_member_opt['dc_member_pi_website_cbox'] and $dc_member_opt['dc_member_pi_website'] != '')    
                        {
                            $out .= '<a class="link-website" target="_blank" href="'.$dc_member_opt['dc_member_pi_website'].'">'.__('Website', CMS_TXT_DOMAIN).'</a>';     
                        }
                        if($dc_member_opt['dc_member_pi_twitter_cbox'] and $dc_member_opt['dc_member_pi_twitter'] != '')    
                        {
                            $out .= '<a class="link-twitter" target="_blank" href="'.$dc_member_opt['dc_member_pi_twitter'].'">'.__('Twitter', CMS_TXT_DOMAIN).'</a>';    
                        }
                        if($dc_member_opt['dc_member_pi_facebook_cbox'] and $dc_member_opt['dc_member_pi_facebook'] != '')    
                        {
                            $out .= '<a class="link-facebook" target="_blank" href="'.$dc_member_opt['dc_member_pi_facebook'].'">'.__('Facebook', CMS_TXT_DOMAIN).'</a>';     
                        }                        
                    $out .= '</div>';  
                }
                
                // content
                $out .= '<div class="ppf-content">'; 

                    if($multipage)
                    {  
                        $out .= apply_filters('the_content', $pages[$page-1]);            
                    } else
                    {
                        $out .= apply_filters('the_content', $dc_post->post_content);                       
                    }   
                                   
                    $out .= '<div class="dc-clear"></div>'; 
                $out .= '</div>'; 
                $out .= GetDCCPI()->getIRenderer()->wpPaginationBlock(false);                       
            
            $out .= '</div>';                  
        }
        
        if($echo) { echo $out; } else { return $out; }    
    }
 
    public function renderMembers($args=array(), $echo=false)
    {
        $def = array(
            'per_page' => 8,
            'cats' => array(),
            'words' => 24,
            'list' => '',           
            'columns' => 1,
            'order' => 'DESC', // DESC, ASC
            'orderby' => 'date', // date, title, comment_count
            'pagination' => false,            
            'viewport_w' => 300,
            'viewport_h' => 400,
            'viewport_use' => false,            
            'item_bottom' => 30,
            'item_bottom_use' => false,
            
            'link_to_single' => true,
            'grayscale' => false,
            
            'title_display' => true, 
            'excerpt' => true,
            'meta_title' => true,
            'meta_subtitle' => true,
            'meta_addinfo' => true,
            'meta_website' => true,
            'meta_twitter' => true,
            'meta_facebook' => true,
            'addinfo_words' => 24
        );
        $args = $this->combineArgs($def, $args);
        
        $terms = array();
        if($args['list'] == '')
        {
            if(!is_array($args['cats'])) { $args['cats'] = array(); }
            if(count($args['cats']) == 0)
            {
                $terms = get_terms(DCC_ControlPanelCustomPosts::PT_MEMBER_CATEGORY, array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                } else 
                {
                    $temp = array();
                    foreach($terms as $cat)
                    {
                        array_push($temp, $cat->term_id);
                    }
                    $args['cats'] = $temp;
                }                        
            } else
            {
                $terms = get_terms(DCC_ControlPanelCustomPosts::PT_MEMBER_CATEGORY, array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                } else
                {
                    $temp = array();
                    foreach($terms as $t)
                    {                                            
                        if(in_array($t->term_id, $args['cats']))
                        {
                            array_push($temp, $t);    
                        }                        
                    }
                    $terms = $temp;
                }    
            }
        }                
        
        $paged = $this->getPagedQueryVar();
        if(!$args['pagination']) { $paged = 1; }
        
        $query_args = array(
            'posts_per_page' => $args['per_page'], 
            'paged' => $paged, 
            'nopaging' => false, 
            'post_status' => 'publish', 
            'ignore_sticky_posts' => false, 
            'post_type' => DCC_ControlPanelCustomPosts::PT_MEMBER_POST,
            'order' => $args['order'],
            'orderby' => $args['orderby']
        );
        
        if($args['list'] != '')
        {
            $query_args['post__in'] = explode(',', $args['list']);
        }
  
        if($args['list'] == '')
        {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => DCC_ControlPanelCustomPosts::PT_MEMBER_CATEGORY,
                    'field' => 'id',
                    'terms' => $args['cats'],
                    'operator' => 'IN'
                )            
            );
        }  
             
        $dc_query = new WP_Query($query_args);
        
        // if list, sort posts by user list order
        if($args['list'] != '')
        {
            if(is_array($dc_query->posts) and count($dc_query->posts))
            {
                $client_ids = explode(',', $args['list']);
                $spl = array();
                foreach($client_ids as $list_id)
                {
                    $list_id = (int)$list_id;
                    foreach($dc_query->posts as $p)
                    {
                        if($list_id == $p->ID)
                        {
                            array_push($spl, $p);
                            break;    
                        }
                    }
                }
                $dc_query->posts = $spl;
            }    
        }  
        
        if($args['columns'] != 1 and $args['columns'] != 2 and 
           $args['columns'] != 3 and $args['columns'] != 4 and 
           $args['columns'] != 5 and $args['columns'] != 6) { $args['columns'] = 1; }                      
        
        $out = ''; 
        $out .= '<div class="dc-member-items-wrapper">';

            for($i = 0; $i < $dc_query->post_count; $i++)
            {            
                $object = new DCC_WPPost($dc_query->posts[$i]);
                $meta = get_post_meta($object->ID, 'dc_member_opt', true);
                $permalink = get_permalink($object->ID);        
                $is_link = $args['link_to_single']; 
                $alt = $meta['dc_member_image_alt'];
                $alt = str_replace(array('"'), '', $alt);                
            
                if($i % $args['columns'] == 0) { $out .= '<div class="dc-clear-both"></div>'; }              
            
                $last_class = '';
                if($args['columns'] > 1)
                {
                    if(($i % $args['columns']) == ($args['columns']-1)) { $last_class = '-last'; }
                }
                
                $last_item_class = '';
                if($i == ($dc_query->post_count-1))              
                {
                    $last_item_class = ' last-item-in-seg';
                }  
                
                $last_line_start_index = dcf_getLastLineIndex($dc_query->post_count, $args['columns']);                              
                $last_line_class = '';                
                if($i >= $last_line_start_index)              
                {
                    $last_line_class = ' last-line-in-seg';
                }                              
            
                $style = '';
                    if($args['item_bottom_use']) { $style .= 'margin-bottom:'.$args['item_bottom'].'px;'; }                    
                $style = ' style="'.$style.'" ';
            
                $out .= '<div class="dc-member-item dc-member-item-1-'.$args['columns'].$last_class.$last_item_class.$last_line_class.'" '.$style.'>';
                
                        // image
                        $filter = $meta['dc_member_image_filter'];
                        if($args['grayscale']) { $filter = CMS_IMAGE_FILTER_GRAYSCALE; }
                
                        $out .= '<div class="top-side">';
                            if($is_link) { $out .= '<a href="'.$permalink.'">'; }  
                                if($args['viewport_use'])
                                {
                                    $out .= '<img src="'.dcf_getImageURL($meta['dc_member_image'], $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $filter, false).'" alt="'.$alt.'" />';    
                                } else
                                {
                                    $out .= '<img src="'.dcf_getImageURL($meta['dc_member_image'], 0, 0, CMS_IMAGE_NOCROP, $filter, true).'" alt="'.$alt.'" />';
                                }
                            if($is_link) { $out .= '</a>'; } 
                        $out .= '</div>';
                        
                        // titles    
                        if($args['title_display'])
                        {                            
                            $out .= '<div class="title">';
                                if($is_link) { $out .= '<a href="'.$permalink.'" >'; }
                                    $out .= $object->post_title;
                                if($is_link) { $out .= '</a>'; }; 
                            $out .= '</div>';
                        }          
                        
                        if($args['meta_title'] and $meta['dc_member_pi_title'] != '')
                        {
                            $out .= '<div class="meta-title">'.$meta['dc_member_pi_title'].'</div>';
                        }              
                        
                        if($args['meta_subtitle'] and $meta['dc_member_pi_subtitle'] != '')
                        {
                            $out .= '<div class="meta-subtitle">'.$meta['dc_member_pi_subtitle'].'</div>';
                        }       
                        
                        if($args['meta_addinfo'] and $meta['dc_member_pi_addinfo'] != '')  
                        {
                            $out .= '<div class="meta-addinfo">'.dcf_strNWords($meta['dc_member_pi_addinfo'], $args['addinfo_words']).'</div>'; 
                        }                        
                        
                        if($args['excerpt'] and $object->post_excerpt != '')
                        {
                            $out .= '<div class="member-excerpt">';
                                $out .= dcf_strNWords($object->post_excerpt, $args['words']);
                            $out .= '</div>';    
                        }                                
                        
                        // social links
                        $web_links_out = '';
                        if($args['meta_website'] and $meta['dc_member_pi_website_cbox'] and $meta['dc_member_pi_website'] != '')
                        {
                            $web_links_out .= '<a class="meta-link meta-link-website" href="'.$meta['dc_member_pi_website'].'" target="_blank">Website</a> ';
                        }             

                        if($args['meta_twitter'] and $meta['dc_member_pi_twitter_cbox'] and $meta['dc_member_pi_twitter'] != '')
                        {
                            $web_links_out .= '<a class="meta-link meta-link-twitter" href="'.$meta['dc_member_pi_twitter'].'" target="_blank">Twitter</a> ';
                        }       
                       
                        if($args['meta_facebook'] and $meta['dc_member_pi_facebook_cbox'] and $meta['dc_member_pi_facebook'] != '')
                        {
                            $web_links_out .= '<a class="meta-link meta-link-facebook" href="'.$meta['dc_member_pi_facebook'].'" target="_blank">Facebook</a> ';
                        }    
                        
                        if($web_links_out != '')
                        {
                            $out .= '<div class="meta-links-wrapper">';
                                $out .= $web_links_out;
                            $out .= '</div>';
                        }                           
                     
                $out .= '</div>';
                
            }
            $out .= '<div class="dc-clear-both"></div>'; 
        $out .= '</div>'; 
        
        if($args['pagination'])
        {
            $out .= $this->wpQueryPaginationBlock(array('paged' => $paged, 'maxpage' => $dc_query->max_num_pages, 'top' => 10, 'pb' => 0));
        } 
        
        if($echo) { echo $out; } else { return $out; }              
    } 

    public function renderNGGBoxGallery($args=array(), $echo=false)
    {
        $def = array(
            'id' => CMS_NOT_SELECTED,
            'per_page' => 6,
            'list' => '',           
            'columns' => 3,
            'exclude' => true,
            'group' => '',
            'order' => 'ASC', // DESC, ASC
            'orderby' => 'sortorder', // sortorder, imagedate
            'pagination' => true,            
            'viewport_w' => 400,
            'viewport_h' => 300,
            'viewport_use' => true,            
            'item_bottom' => 30,
            'item_bottom_use' => false,           
            'grayscale' => false,          
            'title_display' => false, 
            'desc_display' => false, 
            'date_display' => false,
            'meta_size' => false,
            'meta_download' => false 
        );
        $args = $this->combineArgs($def, $args);                     
        
        $paged = $this->getPagedQueryVar();
        if(!$args['pagination']) { $paged = 1; }    
        
        if($args['columns'] != 1 and $args['columns'] != 2 and 
           $args['columns'] != 3 and $args['columns'] != 4 and 
           $args['columns'] != 5 and $args['columns'] != 6 and $args['columns'] != 8) { $args['columns'] = 1; }                      
        
        $out = ''; 
        
        if($args['id'] != CMS_NOT_SELECTED or $args['list'] != '')
        {        
            $gall = null;
            $max_page = 1;
            $start = ($paged-1)*$args['per_page'];            
            
            if($args['list'] != '')
            {
                $gall = dcf_getNGGImagesFromIDList($args['list']);  
                
                if(is_array($gall) and count($gall))
                {
                    $client_ids = explode(',', $args['list']);
                    $spl = array();
                    foreach($client_ids as $list_id)
                    {
                        $list_id = (int)$list_id;
                        foreach($gall as $p)
                        {
                            if($list_id == $p->_pid)
                            {
                                array_push($spl, $p);
                                break;    
                            }
                        }
                    }
                    $max_page = (int)ceil(count($spl) / $args['per_page']); 
                    $gall = array_slice($spl, $start, $args['per_page']);
                }                      
            } else
            if($args['id'] != CMS_NOT_SELECTED)
            {
                $gall = dcf_getGalleryNGG($args['id'], $args['orderby'], $args['order'], $args['exclude'], $args['per_page'], $start, $max_page);    
            }
                                    
            
            if(is_array($gall))
            {
                $count = count($gall);
                if($count)
                {
                    $out .= '<div class="dc-ngg-box-gallery-seg-wrapper">';
                        
                        for($i = 0; $i < $count; $i++)
                        {
                            $img = $gall[$i];
                            $src = $img->_imageURL;
                            $lightbox_group = $img->_thumbcode;
                            if($args['group'] != '') { $lightbox_group = $args['group']; }
                            
                            if($args['grayscale'] or $args['viewport_use'])
                            {
                                $filter = CMS_IMAGE_FILTER_NONE;
                                if($args['grayscale']) { $filter = CMS_IMAGE_FILTER_GRAYSCALE; }
                                
                                if($args['viewport_use'])
                                {
                                    $src = dcf_getImageURL($src, $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $filter, false);
                                } else
                                {
                                    $src = dcf_getImageURL($src, null, null, CMS_IMAGE_NOCROP, $filter, true);
                                }   
                                
                                if($args['grayscale'])
                                {
                                    $img->_imageURL = dcf_getImageURL($img->_imageURL, null, null, CMS_IMAGE_NOCROP, $filter, true);     
                                }
                            }                            
                            
                            if($i % $args['columns'] == 0) { $out .= '<div class="dc-clear-both"></div>'; }              
                        
                            $last_class = '';
                            if($args['columns'] > 1)
                            {
                                if(($i % $args['columns']) == ($args['columns']-1)) { $last_class = '-last'; }
                            }
                            
                            $last_item_class = '';
                            if($i == ($count-1))              
                            {
                                $last_item_class = ' last-item-in-seg';
                            }  
                            
                            $last_line_start_index = dcf_getLastLineIndex($count, $args['columns']);                              
                            $last_line_class = '';                
                            if($i >= $last_line_start_index)              
                            {
                                $last_line_class = ' last-line-in-seg';
                            }                              
                        
                            $style = '';
                                if($args['item_bottom_use']) { $style .= 'margin-bottom:'.$args['item_bottom'].'px;'; }                    
                            $style = ' style="'.$style.'" ';                       
                        
                            $out .= '<div class="item item-1-'.$args['columns'].$last_class.$last_item_class.$last_line_class.'" '.$style.'>';                        
                            
                                $out .= '<div class="top-side">';
                                    $out .= '<div class="img-wrapper">';
                                        $out .= '<a class="trigger" href="'.$img->_imageURL.'" rel="lightbox['.$lightbox_group.']" name="'.$img->_alttext.'">';
                                            $out .= '<img src="'.$src.'" alt="'.$img->_alttext.'" />';
                                        $out .= '</a>';
                                    $out .= '</div>';
                                $out .= '</div>';

                                if($args['title_display'] or $args['desc_display'] or $args['date_display'])
                                {
                                    $out .= '<div class="bottom-side">';
                                        if($args['title_display']) { $out .= '<div class="title">'.$img->_alttext.'</div>'; }
                                        if($args['date_display']) 
                                        { 
                                            $time = strtotime($img->_imagedate); 
                                            $out .= '<div class="date">'.date('F j, Y', $time).'</div>'; 
                                        } 
                                        if($args['desc_display']) { $out .= '<div class="desc">'.$img->_description.'</div>'; }
                                        
                                    $out .= '</div>';
                                }
                            
                                if($args['meta_size'] or $args['meta_download'])   
                                {
                                    $out .= '<div class="meta-wrapper">';
                                        if($args['meta_size']) { $out .= '<div class="meta-size">'.__('Size', CMS_TXT_DOMAIN).': '.$img->_width.'x'.$img->_height.'</div>'; }
                                        if($args['meta_download']) { $out .= '<div class="meta-download"><a href="'.$img->_imageURL.'" target="_blank">'.__('Download', CMS_TXT_DOMAIN).'</a></div>'; }
                                    $out .= '</div>';
                                }
                            
                            $out .= '</div>';
                        }
                        
                        $out .= '<div class="dc-clear-both"></div>'; 
                    $out .= '</div>'; 
                    
                    if($args['pagination'])
                    {
                        $out .= $this->wpQueryPaginationBlock(array('paged' => $paged, 'maxpage' => $max_page, 'top' => 10, 'pb' => 0));
                    }
                }
            } 
        }
        
        if($echo) { echo $out; } else { return $out; }              
    }     
    
    public function wgtTwitter($args=array(), $echo=false)
    {
        $def = array(
            'tweetes_count' => 5, 
            'image_display' => true, 
            'time_offset' => 0,
            'time_format' => 'F j, Y, g:i a', 
            'tweets_count_display' => true, 
            'date_display' => true,
            'data' => null,
            'layout' => 'column', // column, wide
            'left_side_w' => 200 
        );
        $args = $this->combineArgs($def, $args);      
    
        $out = '';
        if(is_array($args['data']))
        {
            $out .= '<div class="dc-wgt-twitter-seg-wrapper">'; 
            if($args['data']['user'] === false or $args['data']['timeline'] === false)
            {
                $out .= __('Twitter service is not available', CMS_TXT_DOMAIN);
                
            } else
            {
                $user = $args['data']['user'];
                $timeline = $args['data']['timeline'];
                
                                
                if($args['layout'] == 'wide') 
                { 
                    $style = '';
                    $style .= 'width:'.$args['left_side_w'].'px;';
                    $style = ' style="'.$style.'" ';
                    
                    $out .= '<div class="left-side" '.$style.'>'; 
                }                
                
                if($args['image_display'])
                {
                    $out .= '<div class="about-user">';
                        $out .= '<div class="profile-image">';
                            $out .= '<img src="'.$user->profile_image_url.'" />';
                        $out .= '</div>';
                        
                        $out .= '<div class="description">';
                            $out .= '<div class="screen-name"><a href="http://twitter.com/'.$user->screen_name.'" target="_blank" >'.$user->screen_name.'</a></div>';
                            $out .= '<div class="fs-wrapper">';
                                $out .= '<table>';
                                $out .= '<tr><td class="count">'.$user->followers_count.'</td><td>followers</td></tr>';
                                if($args['tweets_count_display'])
                                {
                                    $out .= '<tr><td class="count">'.$user->statuses_count.'</td><td>tweets</td></tr>'; 
                                }
                                $out .= '</table>';
                            $out .= '</div>';
                            
                        $out .= '</div>'; 
                        $out .= '<div class="dc-clear-both"></div>';
                    $out .= '</div>';
                }
                
                if($args['layout'] == 'wide') { $out .= '</div>'; }
                
                if(is_array($timeline->tweets))
                {                                        
                    if(count($timeline->tweets))
                    {
                        if($args['layout'] == 'wide') 
                        { 
                            $style = '';
                            $style .= 'margin-left:'.$args['left_side_w'].'px;';
                            $style = ' style="'.$style.'" ';                            
                            $out .= '<div class="right-side" '.$style.'>'; 
                        }
                        $out .= '<ul class="tweets-list">';
                        
                        $max = $args['tweetes_count'];
                        $counter = 0;
                        foreach($timeline->tweets as $t)
                        {
                            $out .= '<li>';
                                $out .= dcf_twitterify($t->text);
                                if($args['date_display'])
                                {
                                    $out .= '<div class="time">';  
                                        $time = strtotime($t->created_at) + $args['time_offset'];
                                        $out .= date($args['time_format'], $time);
                                    $out .= '</div>';
                                }
                            $out .= '</li>';
                            
                            $counter++;                        
                            if($counter >= $max) { break; }
                        }
                        
                        $out .= '</ul>';
                        if($args['layout'] == 'wide') { $out .= '</div>'; }
                    }
                }

            }
            $out .= '</div>';
        }      
    
        if($echo) { echo $out; } else { return $out; }
    }
 
    public function wgtMembers($args=array(), $echo=false)
    {
        $def = array(
            'per_page' => 4,
            'cats' => array(),
            'words' => 24,
            'list' => '',      
            'columns' => 1,     
            'order' => 'DESC', // DESC, ASC
            'orderby' => 'date', // date, title, comment_count          
            'viewport_w' => 300,
            'viewport_h' => 400,
            'viewport_use' => false,                     
            'link_to_single' => true,
            'grayscale' => false,
            'image_width' => 50,
            'meta_title' => false,
            'meta_subtitle' => false,
            'meta_addinfo' => false,
            'addinfo_words' => 24,
            'item_bottom' => 25,
            'item_bottom_use' => false            
        );
        $args = $this->combineArgs($def, $args);
        
        $terms = array();
        if($args['list'] == '')
        {
            if(!is_array($args['cats'])) { $args['cats'] = array(); }
            if(count($args['cats']) == 0)
            {
                $terms = get_terms(DCC_ControlPanelCustomPosts::PT_MEMBER_CATEGORY, array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                } else 
                {
                    $temp = array();
                    foreach($terms as $cat)
                    {
                        array_push($temp, $cat->term_id);
                    }
                    $args['cats'] = $temp;
                }                        
            } else
            {
                $terms = get_terms(DCC_ControlPanelCustomPosts::PT_MEMBER_CATEGORY, array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                } else
                {
                    $temp = array();
                    foreach($terms as $t)
                    {                                            
                        if(in_array($t->term_id, $args['cats']))
                        {
                            array_push($temp, $t);    
                        }                        
                    }
                    $terms = $temp;
                }    
            }
        }                        
        
        $query_args = array(
            'posts_per_page' => $args['per_page'], 
            'paged' => 1, 
            'nopaging' => false, 
            'post_status' => 'publish', 
            'ignore_sticky_posts' => false, 
            'post_type' => DCC_ControlPanelCustomPosts::PT_MEMBER_POST,
            'order' => $args['order'],
            'orderby' => $args['orderby']
        );
        
        if($args['list'] != '')
        {
            $query_args['post__in'] = explode(',', $args['list']);
        }
  
        if($args['list'] == '')
        {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => DCC_ControlPanelCustomPosts::PT_MEMBER_CATEGORY,
                    'field' => 'id',
                    'terms' => $args['cats'],
                    'operator' => 'IN'
                )            
            );
        }  
             
        $dc_query = new WP_Query($query_args);
        
        // if list, sort posts by user list order
        if($args['list'] != '')
        {
            if(is_array($dc_query->posts) and count($dc_query->posts))
            {
                $client_ids = explode(',', $args['list']);
                $spl = array();
                foreach($client_ids as $list_id)
                {
                    $list_id = (int)$list_id;
                    foreach($dc_query->posts as $p)
                    {
                        if($list_id == $p->ID)
                        {
                            array_push($spl, $p);
                            break;    
                        }
                    }
                }
                $dc_query->posts = $spl;
            }    
        }                           
        
        $args['columns'] = (int)$args['columns'];
        if($args['columns'] < 1 or $args['columns'] > 4) { $args['columns'] = 1; }            
        
        $out = ''; 
        $out .= '<div class="dc-wgt-member-seg-wrapper">';

            for($i = 0; $i < $dc_query->post_count; $i++)
            {            
                $object = new DCC_WPPost($dc_query->posts[$i]);
                $meta = get_post_meta($object->ID, 'dc_member_opt', true);
                $permalink = get_permalink($object->ID);        
                $is_link = $args['link_to_single']; 
                $alt = $meta['dc_member_image_alt'];
                $alt = str_replace(array('"'), '', $alt);
                  
                if($i % $args['columns'] == 0) { $out .= '<div class="dc-clear-both"></div>'; }              
            
                $last_class = '';
                if($args['columns'] > 1)
                {
                    if(($i % $args['columns']) == ($args['columns']-1)) { $last_class = '-last'; }
                }
                
                $last_item_class = '';
                if($i == ($dc_query->post_count-1))              
                {
                    $last_item_class = ' last-item-in-seg';
                }  
                
                $last_line_start_index = dcf_getLastLineIndex($dc_query->post_count, $args['columns']);                              
                $last_line_class = '';                
                if($i >= $last_line_start_index)              
                {
                    $last_line_class = ' last-line-in-seg';
                }                              
            
                $style = '';
                    if($args['item_bottom_use']) { $style .= 'margin-bottom:'.$args['item_bottom'].'px;'; }                    
                if($style != '') { $style = ' style="'.$style.'" '; }               
            
                $out .= '<div class="dc-item dc-item-1-'.$args['columns'].$last_class.$last_item_class.$last_line_class.'" '.$style.'>';
                
                    // image
                    $filter = $meta['dc_member_image_filter'];
                    if($args['grayscale']) { $filter = CMS_IMAGE_FILTER_GRAYSCALE; }
            
                    $out .= '<div class="left-side" style="width:'.$args['image_width'].'px;">';
                        if($is_link) { $out .= '<a href="'.$permalink.'">'; }  
                            if($args['viewport_use'])
                            {
                                $out .= '<img src="'.dcf_getImageURL($meta['dc_member_image'], $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $filter, false).'" alt="'.$alt.'" />';    
                            } else
                            {   
                                $out .= '<img src="'.dcf_getImageURL($meta['dc_member_image'], $args['image_width'], $args['image_width'], CMS_IMAGE_CROP_FIT, $filter, false).'" alt="'.$alt.'" />';
                            }
                        if($is_link) { $out .= '</a>'; } 
                    $out .= '</div>';                
                
                    $out .= '<div class="right-side" style="margin-left:'.$args['image_width'].'px;">';
                        $out .= '<div class="title">';
                            if($is_link) { $out .= '<a href="'.$permalink.'">'; } 
                                $out .= $object->post_title;
                            if($is_link) { $out .= '</a>'; } 
                        $out .= '</div>'; 
                        
                        if($args['meta_title'] and $meta['dc_member_pi_title'] != '')
                        {
                            $out .= '<div class="meta-title">'.$meta['dc_member_pi_title'].'</div>';
                        }              
                        
                        if($args['meta_subtitle'] and $meta['dc_member_pi_subtitle'] != '')
                        {
                            $out .= '<div class="meta-subtitle">'.$meta['dc_member_pi_subtitle'].'</div>';
                        }       
                        
                        if($args['meta_addinfo'] and $meta['dc_member_pi_addinfo'] != '' and $args['addinfo_words'] > 0)  
                        {
                            $out .= '<div class="meta-addinfo">'.dcf_strNWords($meta['dc_member_pi_addinfo'], $args['addinfo_words']).'</div>'; 
                        }                          
                        
                        if($object->post_excerpt != '' and $args['words'] > 0)
                        {
                            $out .= '<div class="item-excerpt">';
                                $out .= dcf_strNWords($object->post_excerpt, $args['words']);
                            $out .= '</div>';
                        }                        
                    $out .= '</div>';
                
                $out .= '</div>';                  
                  
                               
            }
            $out .= '<div class="dc-clear-both"></div>'; 
        $out .= '</div>'; 
        
        if($echo) { echo $out; } else { return $out; }              
    }  
 
    public function renderBasicSlider($args=array(), $slides=array(), $echo=false)
    {
        $def = array(
            'auto' => true,             // animate automatically, true or false
            'speed' =>  1000,           // speed of the transition, in milliseconds
            'timeout' => 4000,          // time between slide transitions, in milliseconds
            'pager' => true,            // show pages, true or false
            'pause' => true,            // pause on hover, true or false
            'maxwidth' => 0,            // max width of the slideshow, in pixels  
            'maxwidth_pager' => true,   // apply max width to pager container
            'maxwidth_wrapper' => true, // apply max width to slider inner wrapper
            'transition' => 'fade',     // transition mode, fade, slide
            'nextprev' => true,         // display next, prev buttons
            'bottom' => 20,             // bottom margin in pixels
            'viewport_use' => false,
            'viewport_w' => 600,
            'viewport_h' => 320,
            'title_size' => 3,
            'title_as_h' => false,
            'title_color' => '#FFFFFF'
        );
        $args = $this->combineArgs($def, $args);        
        
        if(!is_array($slides)) { $slides = array(); }
        $count = count($slides);
        
        $out = '';
        if($count)
        {
            $style = '';
            $style .= 'margin-bottom:'.$args['bottom'].'px;';
            $style = ' style="'.$style.'" ';
            
            $out .= '<div class="dc-basic-slider" '.$style.'>';
                $out .= '<div class="slider-options">';
                    $out .= '<span name="auto">'.($args['auto'] ? 'true' : 'false').'</span>'; 
                    $out .= '<span name="speed">'.$args['speed'].'</span>';
                    $out .= '<span name="timeout">'.$args['timeout'].'</span>'; 
                    $out .= '<span name="pager">'.($args['pager'] ? 'true' : 'false').'</span>';
                    $out .= '<span name="pause">'.($args['pause'] ? 'true' : 'false').'</span>';
                    $out .= '<span name="maxwidth">'.$args['maxwidth'].'</span>';
                    $out .= '<span name="maxwidth_pager">'.($args['maxwidth_pager'] ? 'true' : 'false').'</span>';
                    $out .= '<span name="maxwidth_wrapper">'.($args['maxwidth_wrapper'] ? 'true' : 'false').'</span>';   
                    $out .= '<span name="transition">'.$args['transition'].'</span>'; 
                    $out .= '<span name="nextprev">'.($args['nextprev'] ? 'true' : 'false').'</span>';                     
                $out .= '</div>';   
                
                $out .= '<div class="inner-wrapper">';
                
                    $out .= '<ul>';
                        
                        // find first visible slide and display it as ghost
                        foreach($slides as $s)
                        {
                            if($s->_display and $s->_url != '')
                            {
                                $out .= '<li class="slide-ghost">';
                                    if($args['viewport_use'])
                                    {
                                        $out .= '<img src="'.dcf_getImageURL($s->_url, $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, CMS_IMAGE_FILTER_NONE).'" alt="" />';    
                                    } else
                                    {
                                        $out .= '<img src="'.$s->_url.'" alt="" />';
                                    }
                                $out .= '</li>';
                                break;
                            }
                        }
                                            
                        foreach($slides as $s)
                        {
                            if(!$s->_display or $s->_url == '') { continue; }
                            
                            $out .= '<li class="slide">';
                            
                                    if($s->_link_use and $s->_link != '') 
                                    {
                                        $out .= '<a href="'.$s->_link.'" '.($s->_blank ? ' target="_blank" ' : ' target="_self" ').'>';
                                    }
                                                                    
                                        if($args['viewport_use'])
                                        {
                                            $out .= '<img src="'.dcf_getImageURL($s->_url, $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, CMS_IMAGE_FILTER_NONE).'" alt="" />';    
                                        } else
                                        {
                                            $out .= '<img src="'.$s->_url.'" alt="" />';
                                        }
                                    
                                    if($s->_link_use and $s->_link != '') 
                                    {
                                        $out .= '</a>';
                                    }                                 
                                    
                                    if(($s->_title_use and $s->_title != '') or ($s->_desc_use and $s->_desc != ''))
                                    {
                                        $out .= '<div class="description">';
                                            $is_title = false;
                                            if($s->_title_use and $s->_title != '') 
                                            { 
                                                $style = ' style="color:'.$args['title_color'].';" ';
                                                
                                                if($args['title_as_h'])
                                                {
                                                    $out .= '<h'.$args['title_size'].' '.$style.'>'.$s->_title.'</h'.$args['title_size'].'>'; $is_title = true;        
                                                } else
                                                { $out .= '<div class="title" '.$style.'>'.$s->_title.'</div>'; $is_title = true; }
                                                
                                            }
                                            if($s->_desc_use and $s->_desc != '') { $out .= '<div class="text'.($is_title ? ' add-margin-top' : '').'">'.$s->_desc.'</div>'; } 
                                        $out .= '</div>';
                                    }
                            $out .= '</li>';   
                        }
                    $out .= '</ul>';
                
                    $out .= '<div class="nav-next-btn"></div>';
                    $out .= '<div class="nav-prev-btn"></div>';                
                $out .= '</div>';
                         
            $out .= '</div>';                                
        }
        
        if($echo) { echo $out; } else { return $out; } 
    }
 
    public function renderAnnoBox($args=array(), $echo=false)
    {
        $def = array(
            'title' => '',
            'title_use' => true,
            'title_size' => 2, 
            'subtitle' => '',
            'subtitle_use' => true,
            'text' => '',
            'text_use' => true,
            'text_fsize' => 14,
            'text_lheight' => 22,
            'text_font_sizes_use' => false,
            'align' => 'center',

            'btn_a_name' => '',
            'btn_a_link' => '',
            'btn_a_blank' => false,
            'btn_a_display' => false,
        
            'btn_b_name' => '',
            'btn_b_link' => '',
            'btn_b_blank' => false,
            'btn_b_display' => false,
        
            'btn_c_name' => '',
            'btn_c_link' => '',
            'btn_c_blank' => false,
            'btn_c_display' => false,
            
            'btn_color' => '#444444',
            'btn_hcolor' => '#000000',
            'btn_bgcolor' => '#EAEAEA',
            'btn_bghcolor' => '#D2D2D2'            
        );
        $args = $this->combineArgs($def, $args);
        
        $out = '';
        
        $out .= '<div class="dc-anno-box">';
            
            if($args['title_use']) 
            { 
                $style = '';
                    $style .= 'text-align:'.$args['align'].';';
                $style = ' style="'.$style.'" ';
                
                $out .= '<div class="title" '.$style.'>';
                    $out .= '<h'.$args['title_size'].'>'.$args['title'];
                        if($args['subtitle_use'])
                        {
                            $out .= '<span>'.$args['subtitle'].'</span>';
                        }
                    $out .= '</h'.$args['title_size'].'>';
                $out .= '</div>'; 
            }
            
            if($args['text_use']) 
            { 
                $style = '';
                    if($args['text_font_sizes_use'])
                    {
                        $style .= 'font-size:'.$args['text_fsize'].'px;';
                        $style .= 'line-height:'.$args['text_lheight'].'px;';  
                    }
                    $style .= 'text-align:'.$args['align'].';';  
                $style = ' style="'.$style.'" ';  
                
                $out .= '<div class="text" '.$style.'>'.$args['text'].'</div>'; 
            }
            
            if($args['btn_a_display'] or $args['btn_b_display'] or $args['btn_c_display'])
            {
                $style = ' style="background-color:'.$args['btn_bgcolor'].';color:'.$args['btn_color'].';" ';
                $onmouseover = 'onmouseover="this.style.backgroundColor=\''.$args['btn_bghcolor'].'\';this.style.color=\''.$args['btn_hcolor'].'\';"';
                $onmouseout = 'onmouseout="this.style.backgroundColor=\''.$args['btn_bgcolor'].'\';this.style.color=\''.$args['btn_color'].'\';"';
               
                $w_style = '';
                    $w_style .= 'text-align:'.$args['align'].';';
                $w_style = ' style="'.$w_style.'" ';                
                
                $out .= '<div class="btns-wrapper" '.$w_style.'>';
                    if($args['btn_a_display'])
                    {
                        $out .= '<a href="'.$args['btn_a_link'].'" '.$onmouseover.' '.$onmouseout.' '.$style.' '.($args['btn_a_blank'] ? ' target="_blank" ' : '').' class="single-btn">'.$args['btn_a_name'].'</a>';
                    }
                    if($args['btn_b_display'])
                    {
                        $out .= '<a href="'.$args['btn_b_link'].'" '.$onmouseover.' '.$onmouseout.' '.$style.' '.($args['btn_b_blank'] ? ' target="_blank" ' : '').' class="single-btn">'.$args['btn_b_name'].'</a>';
                    }
                    if($args['btn_c_display'])
                    {
                        $out .= '<a href="'.$args['btn_c_link'].'" '.$onmouseover.' '.$onmouseout.' '.$style.' '.($args['btn_c_blank'] ? ' target="_blank" ' : '').' class="single-btn">'.$args['btn_c_name'].'</a>';
                    }                    
                $out .= '</div>';
            }
        $out .= '</div>';
        
        if($echo) { echo $out; } else { return $out; } 
    } 
    
    public function renderNGGRecentSlider($args=array(), $echo=false)
    {
        $def = array(
            'id' => CMS_NOT_SELECTED,
            'pages' => 3,
            'orderby' => 'date', // date, sort, id
            'perpage' => 4, // 1-5     
            'viewport_w' => 400,
            'viewport_h' => 300,
            'alttext' => false,
            'grayscale' => false,
            'desc' => '',
            'desc_use' => false   
        );
        $args = $this->combineArgs($def, $args);                  
       
        $count = $args['pages']*$args['perpage'];
        if($args['id'] == CMS_NOT_SELECTED) { $args['id'] = 0; }
        $images = dcf_getNGGLastImages(0, $count, true, $args['id'], $args['orderby']);       
                        
        $out = '';
        
        if(is_array($images))
        {        
            $out .= '<div class="dc-ngg-recent-slider-seg-wrapper">';
                $out .= '<div class="dc-ngg-recent-slider-seg">';
                
                    $out .= '<div class="page-ghost">';
                        $out .= '<div class="item size-1-'.$args['perpage'].'">';    
                            $out .= '<img src="'.dcf_getImageURL($images[0]->_imageURL, $args['viewport_w'], $args['viewport_h']).'" />';
                        $out .= '</div>';
                        $out .= '<div class="clear-both"></div>';
                    $out .= '</div>';                                                
                            
                    $counter = 0;
                    for($i = 0; $i < $args['pages']; $i++)
                    {       
                        $out .= '<div class="page">';
                            for($j = 1; $j <= $args['perpage']; $j++)
                            {
                                $alttext = ($args['alttext']) ? $images[$counter]->_alttext : '';
                                $filter = ($args['grayscale']) ? CMS_IMAGE_FILTER_GRAYSCALE : CMS_IMAGE_FILTER_NONE;
                                $last = '';
                                if($j == $args['perpage'] and $args['perpage'] > 1) { $last = '-last'; }
                                $out .= '<div class="item size-1-'.$args['perpage'].$last.'">';
                                    $out .= '<a href="'.dcf_getImageURL($images[$counter]->_imageURL, $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $filter, true).'" rel="lightbox[ngg-r-slider-'.$args['id'].']" name="'.$alttext.'">';
                                        $out .= '<img src="'.dcf_getImageURL($images[$counter]->_imageURL, $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $filter).'" alt="'.$alttext.'" />';
                                    $out .= '</a>';
                                $out .= '</div>';
                                $counter++;                        
                            }
                            $out .= '<div class="clear-both"></div>';
                        $out .= '</div>';
                    }
                    
                    $out .= '<div class="pages">';
                        $out .= '<div class="next-btn"></div>';
                        $out .= '<div class="prev-btn"></div>';
                    $out .= '</div>';                
                $out .= '</div>'; 
               
               if($args['desc'] != '' and $args['desc_use'])
               {
                    $out .= '<div class="slider-desc">'.$args['desc'].'</div>';
               }
           $out .= '</div>';                
        }
        
        if($echo) { echo $out; } else { return $out; }    
    }
    
    public function renderRecentPostsList($args=array(), $echo=false)
    {     
        $def = array(
            'cats' => array(),
            'list' => '',             
            'count' => 3,             
            'words' => 16,          
            'order' => 'DESC', // DESC, ASC
            'orderby' => 'date', // date, title, comment_count
            'grayscale' => false,
            'excerpt_use' => true,
            'elapsed_time' => false,
            'layout' => 'classic', // classic, box
            'columns' => 4,
            'item_bottom' => 30,
            'item_bottom_use' => false,
            'viewport_w' => 400,
            'viewport_h' => 400,
            'viewport_use' => true,  
            'title_display' => true,
            'date_display' => true,
            'excerpt_display' => true         
        );
        $args = $this->combineArgs($def, $args);
        
        $terms = array();
        if($args['list'] == '')
        {
            if(!is_array($args['cats'])) { $args['cats'] = array(); }
            if(count($args['cats']) == 0)
            {
                $terms = get_terms('category', array('orderby' => 'count', 'hide_empty' => true));
                
                if(!is_array($terms)) 
                { 
                    $terms = array(); 
                } else 
                {
                    $temp = array();
                    foreach($terms as $cat)
                    {
                        array_push($temp, $cat->term_id);
                    }
                    $args['cats'] = $temp;
                }                        
            }
        }         
        
        $query_args = array(
            'posts_per_page' => $args['count'], 
            'paged' => 1, 
            'nopaging' => false, 
            'post_status' => 'publish', 
            'ignore_sticky_posts' => false, 
            'post_type' => 'post',
            'order' => $args['order'],
            'orderby' => $args['orderby']
        );        
        
        if($args['list'] != '')
        {
            $query_args['post__in'] = explode(',', $args['list']);
        }
  
        if($args['list'] == '')
        {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $args['cats'],
                    'operator' => 'IN'
                )            
            );
        }  
             
        $dc_query = new WP_Query($query_args);          
        
        if($args['columns'] != 1 and $args['columns'] != 2 and 
           $args['columns'] != 3 and $args['columns'] != 4) { $args['columns'] = 1; }              
        
        $out = '';    
        
        if($args['layout'] == 'classic')
        {        
            $out .= '<div class="dc-recent-posts-list-seg">';
            
                for($i = 0; $i < $dc_query->post_count; $i++)
                {            
                    $p = new DCC_WPPost($dc_query->posts[$i]);
                    $meta = get_post_meta($p->ID, 'post_opt', true);
                    $permalink = get_permalink($p->ID);
                    $date_format = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_format');  
                   
                    $image_url = $meta['post_image'];
                   
                    if(GetDCCPI()->getIGeneral()->getOption('theme_use_post_wp_thumbnail'))
                    {
                        $t_url = $this->getPostThumbnailURL($p->ID);
                        if($t_url !== false)
                        {
                            $image_url = $t_url;
                        }              
                    }    
                    if($meta['post_image_hide_cbox']) { $image_url = ''; }    
                    
                    $filter = $meta['post_image_filter'];
                    if($args['grayscale']) { $filter = CMS_IMAGE_FILTER_GRAYSCALE; }
        
                    $last_item_class = '';
                    if($i == ($dc_query->post_count-1))              
                    {
                        $last_item_class = ' last-item-in-seg';
                    }  
                    
                    $last_line_start_index = dcf_getLastLineIndex($dc_query->post_count, $args['columns']);                              
                    $last_line_class = '';                
                    if($i >= $last_line_start_index)              
                    {
                        $last_line_class = ' last-line-in-seg';
                    }           
                    
                    $style = '';
                        if($args['item_bottom_use']) { $style .= 'margin-bottom:'.$args['item_bottom'].'px;'; }                    
                    $style = ' style="'.$style.'" ';                                      
                    
                    $out .= '<div class="item'.$last_item_class.'" '.$style.'>';
                    
                        $rs_class = '';
                        if($image_url != '')
                        {
                            $out .= '<div class="left-side">';
                                $out .= '<a href="'.$permalink.'">';
                                    if($args['viewport_use'])
                                    {
                                        $out .= '<img src="'.dcf_getImageURL($image_url, $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $filter, false).'" />';           
                                    } else
                                    {
                                        $out .= '<img src="'.dcf_getImageURL($image_url, 400, 400, CMS_IMAGE_CROP_FIT, $filter, true).'" />';
                                    }
                                $out .= '</a>';
                            $out .= '</div>';             
                        } else
                        {
                            $rs_class = 'full-width';
                        }
                        
                        $out .= '<div class="right-side '.$rs_class.'">'; 
                        
                            if($args['title_display'])
                            {
                                $out .= '<div class="title"><a href="'.$permalink.'">'.$p->post_title.'</a></div>';                    
                            }
                        
                            if($args['date_display'])
                            {
                                if($args['elapsed_time'])
                                {
                                    $out .= '<div class="date">'.dcf_getPastTime($p->post_date).'</div>';  
                                } else
                                {
                                    $out .= '<div class="date">'.mysql2date($date_format, $p->post_date_gmt).'</div>';
                                }
                            }
                           
                            if($args['excerpt_display'])
                            {
                                if($p->post_excerpt != '' and $args['excerpt_use'])
                                {
                                    $out .= '<div class="excerpt">'.dcf_strNWords($p->post_excerpt, $args['words']).'</div>';
                                }
                            }
                        $out .= '</div>';
                        
                        $out .= '<div class="dc-clear-both"></div>';
                    $out .= '</div>';
                }
            $out .= '</div>';  
        } else
        {
            $out .= '<div class="dc-recent-posts-list-box-seg">';
            
                for($i = 0; $i < $dc_query->post_count; $i++)
                {            
                    $p = new DCC_WPPost($dc_query->posts[$i]);
                    $meta = get_post_meta($p->ID, 'post_opt', true);
                    $permalink = get_permalink($p->ID);
                    $date_format = GetDCCPI()->getIGeneral()->getOption('blog_infobar_date_format');  
                   
                    $image_url = $meta['post_image'];
                   
                    if(GetDCCPI()->getIGeneral()->getOption('theme_use_post_wp_thumbnail'))
                    {
                        $t_url = $this->getPostThumbnailURL($p->ID);
                        if($t_url !== false)
                        {
                            $image_url = $t_url;
                        }              
                    }       
                    
                    $filter = $meta['post_image_filter'];
                    if($args['grayscale']) { $filter = CMS_IMAGE_FILTER_GRAYSCALE; }
                    
                    if($i % $args['columns'] == 0) { $out .= '<div class="dc-clear-both"></div>'; }     
                    
                    $last_class = '';
                    if($args['columns'] > 1)
                    {
                        if(($i % $args['columns']) == ($args['columns']-1)) { $last_class = '-last'; }
                    }
                    
                    $last_item_class = '';
                    if($i == ($dc_query->post_count-1))              
                    {
                        $last_item_class = ' last-item-in-seg';
                    }  
                    
                    $last_line_start_index = dcf_getLastLineIndex($dc_query->post_count, $args['columns']);                              
                    $last_line_class = '';                
                    if($i >= $last_line_start_index)              
                    {
                        $last_line_class = ' last-line-in-seg';
                    }                       
                    
                    $style = '';
                        if($args['item_bottom_use']) { $style .= 'margin-bottom:'.$args['item_bottom'].'px;'; }                    
                    $style = ' style="'.$style.'" ';                    
                    
                    $out .= '<div class="item-1-'.$args['columns'].$last_class.$last_item_class.$last_line_class.'" '.$style.'>'; 

                        if($image_url != '')
                        {
                            $size = dcf_getImageSize($image_url); 
                            
                            $out .= '<div class="top-side">';
                                $out .= '<div class="image-wrapper" style="max-width:'.$size['w'].'px;">';
                                    $out .= '<a href="'.$permalink.'">';
                                        if($args['viewport_use'])
                                        {
                                            $out .= '<img src="'.dcf_getImageURL($image_url, $args['viewport_w'], $args['viewport_h'], CMS_IMAGE_CROP_FIT, $filter, false).'" />';           
                                        } else
                                        {
                                            $out .= '<img src="'.dcf_getImageURL($image_url, 400, 400, CMS_IMAGE_CROP_FIT, $filter, true).'" />';
                                        }
                                    $out .= '</a>';
                                $out .= '</div>';
                            $out .= '</div>';             
                        }     
                        
                        $out .= '<div class="bottom-side '.$rs_class.'">'; 
                            if($args['title_display'])   
                            {
                                $out .= '<div class="title"><a href="'.$permalink.'">'.$p->post_title.'</a></div>';                    
                            }
                            
                            if($args['date_display'])
                            {   
                                if($args['elapsed_time'])
                                {
                                    $out .= '<div class="date">'.dcf_getPastTime($p->post_date).'</div>';  
                                } else
                                {
                                    $out .= '<div class="date">'.mysql2date($date_format, $p->post_date_gmt).'</div>';
                                }
                            }

                            if($args['excerpt_display'])
                            {                              
                                if($p->post_excerpt != '' and $args['excerpt_use'])
                                {
                                    $out .= '<div class="excerpt">'.dcf_strNWords($p->post_excerpt, $args['words']).'</div>';
                                }
                            }
                        $out .= '</div>';                                       
                    
                    $out .= '</div>';                         
                }
                
                $out .= '<div class="dc-clear-both"></div>'; 
            $out .= '</div>';                                
        }
         
             
        if($echo) { echo $out; } else { return $out; }    
    }
    
    public function renderDownloadSeg($args=array(), $echo=false)
    {
        $def = array(
            'title' => '',
            'title_use' => false,
            'title_size' => 4, 
            'subtitle' => '',            
            'desc' => '',             
            'desc_use' => false,
            'file_name' => '',
            'file_name_use' => false,
            'file_size' => '',
            'file_size_use' => false,
            'file_url' => '',
            'icon_url' => '',
            'icon_use' => false,
            'icon_w' => 64,
            'btn_pos' => 'left',
            'arrow_display' => false
        );
        $args = $this->combineArgs($def, $args);       
        
        $out = ''; 
        $class = 'to-'.$args['btn_pos'];

        $out .= '<div class="dc-download-btn-seg-wrapper">';
           if($args['title_use'] and $args['title'] != '')
           {
                $out .= '<h'.$args['title_size'].' class="title">'.$args['title'];
                    if($args['subtitle'] != '') { $out .= '<span>'.$args['subtitle'].'</span>'; }
                $out .= '</h'.$args['title_size'].'>';
           }
           if($args['desc_use'] and $args['desc'] != '') 
           {
                $out .= '<div class="description">';
                    $out .= $args['desc'];
                $out .= '</div>';
           }

           $out .= '<div class="semi-wrapper">';
               if($args['btn_pos'] == 'left' or $args['btn_pos'] == 'center')
               {
                   if($args['icon_use'] and $args['icon_url'] != '')
                   {
                        $out .= '<a class="image-icon '.$class.'" style="width:'.$args['icon_w'].'px;" href="'.$args['file_url'].'"><img src="'.$args['icon_url'].'" /></a>';  
                   }               
               }
                       
               $out .= '<div class="dc-download-btn '.$class.'" >';                 
                    
                    if(($args['file_name_use'] and $args['file_name'] != '') or ($args['file_size_use'] and $args['file_size'] != ''))
                    { 
                        $out .= '<div class="left-side">';
                            if($args['file_name_use'] and $args['file_name'] != '') { $out .= '<div class="name"><a href="'.$args['file_url'].'">'.$args['file_name'].'</a></div>'; }
                            if($args['file_size_use'] and $args['file_size'] != '') { $out .= '<div class="size">'.$args['file_size'].'</div>'; } 
                        $out .= '</div>';
                    }
                    
                    if($args['arrow_display'])
                    {
                        $out .= '<a href="'.$args['file_url'].'" class="arrow"></a>';
                    }
                    $out .= '<div class="dc-clear-both"></div>';        
               $out .= '</div>';       
       
               if($args['btn_pos'] == 'right')
               {
                   if($args['icon_use'] and $args['icon_url'] != '')
                   {
                        $out .= '<a class="image-icon '.$class.'" style="width:'.$args['icon_w'].'px;" href="'.$args['file_url'].'"><img src="'.$args['icon_url'].'" /></a>';  
                   }               
               }
                
               $out .= '<div class="dc-clear-both"></div>';
           $out .= '</div>';
        $out .= '</div>';   

        if($echo) { echo $out; } else { return $out; }         
    }
    
    /*********************************************************** 
    * Private functions
    ************************************************************/      
      
}
        
        
?>