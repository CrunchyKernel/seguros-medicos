<?php

class BlogContenidoSeeder extends Seeder {

    public function run()
    {
         $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                    Praesent vel ligula scelerisque, vehicula dui eu, fermentum velit. 
                    Phasellus ac ornare eros, quis malesuada augue. Nunc ac nibh at mauris dapibus fermentum. 
                    In in aliquet nisi, ut scelerisque arcu. Integer tempor, nunc ac lacinia cursus, 
                    mauris justo volutpat elit, 
                    eget accumsan nulla nisi ut nisi. Etiam non convallis ligula. Nulla urna augue, 
                    dignissim ac semper in, ornare ac mauris. Duis nec felis mauris.';

        Blog::where('id_blog', '>=', 100)->delete();
        DB::unprepared("ALTER TABLE gm_blog AUTO_INCREMENT = 100;");
        
        for($i=1;$i<=5000;$i++){
        	$idBlogCategoria = (rand(0,31) + 1);
        	$categoria = Blogcategoria::find($idBlogCategoria);
            $post = new Blog;
            $post->titulo = $categoria->categoria." [ post $i ]";
            $post->alias = strtolower(trim(str_replace(" ", "-", trim(str_replace("[ ", "", trim(str_replace(" ]", "", trim($post->titulo))) )) )) );
            $post->introtext = substr($content, 0, 120);
            $post->id_blog_categoria = $idBlogCategoria;
            $post->contenido = $content;
            $post->fecha_publicacion = date('Y-m-d', mt_rand(strtotime(date('2014-04-25')), strtotime(date('Y-m-d'))) );
            $post->save();
        }
        
    }

}
