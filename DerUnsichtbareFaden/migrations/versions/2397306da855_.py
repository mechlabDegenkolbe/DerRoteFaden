"""empty message

Revision ID: 2397306da855
Revises: None
Create Date: 2015-03-18 00:41:14.279151

"""

# revision identifiers, used by Alembic.
revision = '2397306da855'
down_revision = None

from alembic import op
import sqlalchemy as sa


def upgrade():
    ### commands auto generated by Alembic - please adjust! ###
    op.create_table('article',
    sa.Column('id', sa.Integer(), nullable=False),
    sa.Column('name', sa.Unicode(length=256), nullable=True),
    sa.Column('text', sa.UnicodeText(), nullable=True),
    sa.Column('date', sa.DateTime(), nullable=True),
    sa.Column('texture', sa.Text(), nullable=True),
    sa.Column('book', sa.Integer(), nullable=False),
    sa.Column('active', sa.Boolean(), nullable=True),
    sa.Column('count', sa.Integer(), nullable=True),
    sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_article_name'), 'article', ['name'], unique=False)
    op.create_index(op.f('ix_article_text'), 'article', ['text'], unique=False)
    op.create_table('user',
    sa.Column('id', sa.Integer(), nullable=False),
    sa.Column('name', sa.Unicode(length=256), nullable=True),
    sa.Column('password', sa.String(length=512), nullable=False),
    sa.PrimaryKeyConstraint('id')
    )
    op.create_table('node',
    sa.Column('id', sa.Integer(), nullable=False),
    sa.Column('name', sa.Unicode(length=256), nullable=True),
    sa.Column('pos_x', sa.Float(), nullable=True),
    sa.Column('pos_y', sa.Float(), nullable=True),
    sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_node_name'), 'node', ['name'], unique=False)
    op.create_index(op.f('ix_node_pos_x'), 'node', ['pos_x'], unique=False)
    op.create_index(op.f('ix_node_pos_y'), 'node', ['pos_y'], unique=False)
    op.create_table('symbol',
    sa.Column('id', sa.Integer(), nullable=False),
    sa.Column('name', sa.Unicode(length=64), nullable=True),
    sa.Column('icon', sa.Unicode(length=8), nullable=True),
    sa.PrimaryKeyConstraint('id')
    )
    op.create_table('comment',
    sa.Column('id', sa.Integer(), nullable=False),
    sa.Column('name', sa.Unicode(length=256), nullable=True),
    sa.Column('email', sa.Unicode(length=256), nullable=True),
    sa.Column('date', sa.DateTime(), nullable=True),
    sa.Column('text', sa.Text(), nullable=True),
    sa.Column('new', sa.Boolean(), nullable=True),
    sa.Column('article_id', sa.Integer(), nullable=True),
    sa.ForeignKeyConstraint(['article_id'], ['article.id'], ),
    sa.PrimaryKeyConstraint('id')
    )
    op.create_table('node_links',
    sa.Column('source', sa.Integer(), nullable=True),
    sa.Column('target', sa.Integer(), nullable=True),
    sa.ForeignKeyConstraint(['source'], ['node.id'], ),
    sa.ForeignKeyConstraint(['target'], ['node.id'], )
    )
    op.create_table('article_nodes',
    sa.Column('article_id', sa.Integer(), nullable=True),
    sa.Column('node_id', sa.Integer(), nullable=True),
    sa.ForeignKeyConstraint(['article_id'], ['article.id'], ),
    sa.ForeignKeyConstraint(['node_id'], ['node.id'], )
    )
    op.create_table('symbol_links',
    sa.Column('source', sa.Integer(), nullable=True),
    sa.Column('target', sa.Integer(), nullable=True),
    sa.ForeignKeyConstraint(['source'], ['symbol.id'], ),
    sa.ForeignKeyConstraint(['target'], ['symbol.id'], )
    )
    ### end Alembic commands ###


def downgrade():
    ### commands auto generated by Alembic - please adjust! ###
    op.drop_table('symbol_links')
    op.drop_table('article_nodes')
    op.drop_table('node_links')
    op.drop_table('comment')
    op.drop_table('symbol')
    op.drop_index(op.f('ix_node_pos_y'), table_name='node')
    op.drop_index(op.f('ix_node_pos_x'), table_name='node')
    op.drop_index(op.f('ix_node_name'), table_name='node')
    op.drop_table('node')
    op.drop_table('user')
    op.drop_index(op.f('ix_article_text'), table_name='article')
    op.drop_index(op.f('ix_article_name'), table_name='article')
    op.drop_table('article')
    ### end Alembic commands ###