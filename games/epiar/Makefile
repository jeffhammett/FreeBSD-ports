# Created by: Rusmir Dusko <nemysis@FreeBSD.org>
# $FreeBSD$

PORTNAME=	epiar
PORTVERSION=	0.5.1
PORTREVISION=	5
CATEGORIES=	games

MAINTAINER=	ports@FreeBSD.org
COMMENT=	Space trading/exploring sim with RPG elements

LICENSE=	GPLv2 CC-BY-NC-SA-3.0
LICENSE_COMB=	multi
LICENSE_FILE_CC-BY-NC-SA-3.0=	${WRKSRC}/LICENSE.music

LIB_DEPENDS=	libfreetype.so:print/freetype2 \
		libftgl.so:graphics/ftgl \
		libxml2.so:textproc/libxml2 \
		libphysfs.so:devel/physfs

USE_GITHUB=	yes
GH_ACCOUNT=	knowknowledge
GH_PROJECT=	Epiar
GH_TAGNAME=	44046e3

USES=		autoreconf gmake pkgconfig
USE_SDL=	image mixer sdl
USE_GL=		glu
GNU_CONFIGURE=	yes

PORTDOCS=	AUTHORS ChangeLog README
OPTIONS_DEFINE=	DOCS

# Wrong versioning upstream
PORTSCOUT=	ignore:1

post-install:
	${INSTALL_DATA} ${WRKSRC}/${PORTNAME}.desktop \
		${STAGEDIR}${DESKTOPDIR}

	@${MKDIR} ${STAGEDIR}${DOCSDIR}
	${INSTALL_DATA} ${PORTDOCS:S|^|${WRKSRC}/|} ${STAGEDIR}${DOCSDIR}

.include <bsd.port.mk>
