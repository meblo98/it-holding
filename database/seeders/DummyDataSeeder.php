<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Project;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // Seed Services
        $services = [
            [
                'title' => 'Développement Logiciel Sur Mesure',
                'description' => 'Conception et développement d\'applications métier personnalisées, robustes et évolutives pour répondre à vos défis spécifiques.',
                'content' => 'Notre équipe d\'experts vous accompagne dans toutes les phases de votre projet : de l\'analyse des besoins à la mise en production, en passant par le design UX/UI et le développement agile. Nous utilisons les technologies les plus modernes pour garantir performance et sécurité.',
                'icon' => 'code',
                'image' => null,
            ],
            [
                'title' => 'Infrastructure Cloud & DevOps',
                'description' => 'Optimisez vos opérations avec nos solutions Cloud sur mesure. Migration, gestion d\'infrastructure et automatisation DevOps.',
                'content' => 'Passez au niveau supérieur avec une infrastructure Cloud performante. Nous vous aidons à migrer vos services vers AWS, Azure ou Google Cloud, tout en mettant en place des pipelines CI/CD automatisés pour accélérer vos déploiements.',
                'icon' => 'cloud',
                'image' => null,
            ],
            [
                'title' => 'Cybersécurité & Audit',
                'description' => 'Protégez vos actifs les plus précieux. Audits complets, tests de pénétration et mise en place de stratégies de défense robustes.',
                'content' => 'La sécurité n\'est pas une option. Nos experts en cybersécurité réalisent des audits approfondis de vos systèmes pour identifier les vulnérabilités et mettre en place des protocoles de protection avancés.',
                'icon' => 'shield-check',
                'image' => null,
            ],
            [
                'title' => 'Intelligence Artificielle & Big Data',
                'description' => 'Transformez vos données en décisions stratégiques grâce à nos solutions avancées d\'analyse de données et d\'IA.',
                'content' => 'Exploitez tout le potentiel de vos données. Nous concevons des modèles de machine learning et des tableaux de bord d\'analytics pour vous donner une vision claire et prédictive de votre activité.',
                'icon' => 'database',
                'image' => null,
            ],
            [
                'title' => 'Transformation Digitale',
                'description' => 'Accompagnement stratégique pour moderniser vos processus métier et adopter les nouveaux outils numériques.',
                'content' => 'Réinventez votre façon de travailler. Nous vous conseillons sur les meilleurs outils et stratégies pour digitaliser vos processus et améliorer la collaboration au sein de vos équipes.',
                'icon' => 'refresh',
                'image' => null,
            ],
            [
                'title' => 'Maintenance & Support IT',
                'description' => 'Support technique réactif et maintenance préventive pour assurer la continuité de vos services critiques.',
                'content' => 'Concentrez-vous sur votre cœur de métier, nous nous occupons du reste. Notre support 24/7 et nos contrats de maintenance garantissent que vos outils de travail sont toujours opérationnels.',
                'icon' => 'support',
                'image' => null,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => Str::slug($service['title'])],
                array_merge($service, ['active' => true])
            );
        }

        // Seed Projects
        $projects = [
            [
                'title' => 'Plateforme E-commerce IT-Sales',
                'description' => 'Développement d\'une solution de vente en ligne complète pour un leader de la distribution informatique.',
                'client' => 'TechGroup Africa',
                'completion_date' => '2023-10-15',
                'technologies' => ['Laravel', 'Vue.js', 'TailwindCSS', 'PostgreSQL'],
                'url' => 'https://example.com/project1',
            ],
            [
                'title' => 'Système de Gestion de Stock Cloud',
                'description' => 'Migration et optimisation d\'un logiciel de gestion d\'entrepôt vers une architecture Cloud micro-services.',
                'client' => 'Logistics Pro',
                'completion_date' => '2023-12-05',
                'technologies' => ['AWS', 'Docker', 'Go', 'Redis'],
                'url' => 'https://example.com/project2',
            ],
            [
                'title' => 'Application Mobile SantéConnect',
                'description' => 'Conception d\'une application de télémédecine permettant la prise de rendez-vous et le suivi médical en ligne.',
                'client' => 'HealthCare Innovations',
                'completion_date' => '2024-01-20',
                'technologies' => ['Flutter', 'Firebase', 'Node.js'],
                'url' => 'https://example.com/project3',
            ],
            [
                'title' => 'Audit de Sécurité Bancaire',
                'description' => 'Mission de test d\'intrusion et renforcement de l\'infrastructure réseau pour une institution financière.',
                'client' => 'Banque de l\'Ouest',
                'completion_date' => '2024-02-10',
                'technologies' => ['Kali Linux', 'Wireshark', 'Python'],
                'url' => null,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['slug' => Str::slug($project['title'])],
                $project
            );
        }
    }
}
