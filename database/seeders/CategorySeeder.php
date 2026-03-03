<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Content\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categorias para artigos
        $articleCategories = [
            [
                'name' => 'Fé',
                'description' => 'Notícias e artigos sobre fé.',
                'belongs_to' => 'article',
                'icon' => 'sparkles',
                'color' => '#3B82F6',
                'children' => []
            ],
            [
                'name' => 'Sharia',
                'description' => 'Legislação islâmica e jurisprudência.',
                'belongs_to' => 'article',
                'icone' => 'scale',
                'color' => '#10B981',
                'children' => []
            ],
            [
                'name' => 'História',
                'description' => 'Eventos históricos, biografias e análises.',
                'belongs_to' => 'article',
                'icon' => 'landmark',
                'color' => '#F59E0B',
                'children' => []
            ],
            [
                'name' => 'Família',
                'description' => 'Assuntos relacionados à família e relacionamentos.',
                'belongs_to' => 'article',
                'icon' => 'heart',
                'color' => '#EC4899',
                'children' => []
            ],
            [
                'name' => 'Ciência',
                'description' => 'Descobertas científicas, avanços tecnológicos e relação com o islam',
                'belongs_to' => 'article',
                'icon' => 'atom',
                'color' => '#06B6D4',
                'children' => []
            ],
            [
                'name' => 'Sociedade',
                'description' => 'Questões sociais, cultura e atualidades.',
                'belongs_to' => 'article',
                'icon' => 'users',
                'color' => '#F97316',
                'children' => []
            ],
            [
                'name' => 'Educação',
                'description' => 'Métodos educacionais, escolas e aprendizado.',
                'belongs_to' => 'article',
                'icon' => 'graduation-cap',
                'color' => '#10B981',
                'children' => []
            ],
            [
                'name' => 'Ramadan',
                'description' => 'Tradições, receitas e eventos relacionados ao Ramadan.',
                'belongs_to' => 'article',
                'icon' => 'moon',
                'color' => '#22C55E',
                'children' => []
            ],
            [
                'name' => 'Juventude',
                'description' => 'Assuntos voltados para jovens muçulmanos.',
                'belongs_to' => 'article',
                'icon' => 'book-open',
                'color' => '#F43F5E',
                'children' => []
            ],
        ];

        // categorias para vídeos
        $videoCategories = [
            [
                'name' => 'Palestras',
                'description' => 'Palestras e discursos de estudiosos e líderes.',
                'belongs_to' => 'video',
                'color' => '#3B82F6',
                'children' => []
            ],
            [
                'name' => 'Khutbahs',
                'description' => 'Khutbahs (discursos) de líderes religiosos e estudiosos.',
                'belongs_to' => 'video',
                'color' => '#10B981',
                'children' => []
            ],
            [
                'name' => 'Séries',
                'description' => 'Séries documentais e educacionais sobre temas islâmicos.',
                'belongs_to' => 'video',
                'color' => '#F59E0B',
                'children' => []
            ],
            [
                'name' => 'Debates',
                'description' => 'Debates entre estudiosos e especialistas.',
                'belongs_to' => 'video',
                'color' => '#EC4899',
                'children' => []
            ],
            [
                'name' => 'Entrevistas',
                'description' => 'Entrevistas com líderes, estudiosos e membros da comunidade.',
                'belongs_to' => 'video',
                'color' => '#06B6D4',
                'children' => []
            ],
            [
                'name' => 'Documentários',
                'description' => 'Documentários sobre história, cultura e temas islâmicos.',
                'belongs_to' => 'video',
                'color' => '#F97316',
                'children' => []
            ]
        ];

        $categories = array_merge($articleCategories, $videoCategories);

        foreach ($categories as $index => $data) {
            $parent = Category::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'belongs_to' => $data['belongs_to'],
                'icon' => $data['icon'] ?? null,
                'color' => $data['color'],
                'order' => $index,
                'is_active' => true,
            ]);

            foreach ($data['children'] as $childIndex => $child) {
                Category::create([
                    'name' => $child['name'],
                    'slug' => Str::slug($child['name']),
                    'description' => null,
                    'color' => $data['color'],
                    'parent_id' => $parent->id,
                    'order' => $childIndex,
                    'is_active' => true,
                ]);
            }
        }
    }
}
