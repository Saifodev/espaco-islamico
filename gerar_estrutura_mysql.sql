-- mysql -u root -p -N -e "$(cat gerar_estrutura_mysql.sql)" > estrutura_tabelas.sql

-- gerar_estrutura_mysql.sql
-- Versão mais completa incluindo PRIMARY KEY e índices
SELECT 
    CONCAT(
        'CREATE TABLE `', table_name, '` (\n',
        -- Colunas
        GROUP_CONCAT(
            CONCAT('  `', column_name, '` ', column_type,
                CASE WHEN is_nullable = 'NO' THEN ' NOT NULL' ELSE '' END,
                CASE 
                    WHEN column_default IS NOT NULL THEN 
                        CONCAT(' DEFAULT ', 
                            CASE 
                                WHEN column_default IN ('CURRENT_TIMESTAMP') 
                                THEN column_default
                                ELSE QUOTE(column_default)
                            END
                        )
                    ELSE ''
                END,
                CASE WHEN extra = 'auto_increment' THEN ' AUTO_INCREMENT' ELSE '' END
            )
            ORDER BY ordinal_position
            SEPARATOR ',\n'
        ),
        -- Primary Key (se existir)
        COALESCE(
            CONCAT(',\n  PRIMARY KEY (',
                (SELECT GROUP_CONCAT(CONCAT('`', column_name, '`') ORDER BY ordinal_position)
                 FROM information_schema.key_column_usage k
                 WHERE k.table_schema = c.table_schema 
                   AND k.table_name = c.table_name
                   AND k.constraint_name = 'PRIMARY'),
                ')'
            ), ''
        ),
        '\n) ENGINE=', engine, ' DEFAULT CHARSET=', 
        SUBSTRING_INDEX(table_collation, '_', 1), ';\n\n'
    ) AS create_table_statement
FROM information_schema.columns c
JOIN information_schema.tables t 
    ON t.table_name = c.table_name 
    AND t.table_schema = c.table_schema
WHERE c.table_schema = 'portal'
GROUP BY c.table_name, t.engine, t.table_collation
ORDER BY c.table_name;