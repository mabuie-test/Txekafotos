USE txekafotos;

INSERT INTO admins (name, email, password_hash)
VALUES (
    'Administrador Txekafotos',
    'admin@txekafotos.com',
    '$2y$12$sdRuqaxkWfP752ZAIC/PsOleUXl/2VQLDiFkXHfeVkJAl6QZvGpwO'
);

INSERT INTO homepage_content (
    hero_title,
    hero_subtitle,
    hero_cta_text,
    hero_secondary_cta_text,
    hero_badge_text,
    section_benefits_title,
    section_feedback_title,
    section_showcase_title,
    final_cta_title,
    final_cta_text,
    benefits_text,
    stats_json
) VALUES (
    'Restaure memórias especiais por apenas 45 MZN',
    'Envie a sua foto, descreva a transformação desejada e acompanhe tudo com pagamento seguro por M-Pesa.',
    'Enviar foto agora',
    'Acompanhar pedido',
    'Pagamento seguro via M-Pesa',
    'Porque as famílias escolhem a Txekafotos',
    'O que os clientes dizem',
    'Antes e depois que emocionam',
    'Pronto para recuperar uma memória importante?',
    'Nossa equipa transforma fotografias antigas, danificadas ou incompletas em lembranças prontas para guardar e partilhar.',
    'Qualidade profissional, fluxo transparente, revisões controladas e apoio humano durante todo o processo.',
    JSON_OBJECT('clientes_satisfeitos', 1280, 'avaliacao_media', 4.9, 'pedidos_entregues', 3400)
);

INSERT INTO marketing_banners (title, subtitle, button_text, button_link, is_active)
VALUES
('Promoção de lançamento', 'Peça sua primeira restauração com acompanhamento completo.', 'Criar pedido', '/pedido/criar', 1),
('Família reunida em uma única imagem', 'Montagens afetivas com acabamento profissional.', 'Ver exemplos', '/#showcases', 1);
