# Created by: Dryice Liu <dryice@dryice.name>
# $FreeBSD$

PORTNAME=	mako
PORTVERSION=	1.0.4
CATEGORIES=	textproc www python
MASTER_SITES=	CHEESESHOP
PKGNAMEPREFIX=	${PYTHON_PKGNAMEPREFIX}
DISTNAME=	Mako-${PORTVERSION}

MAINTAINER=	wen@FreeBSD.org
COMMENT=	Super-fast templating language in Python

LICENSE=	MIT

BUILD_DEPENDS=	${PYTHON_PKGNAMEPREFIX}beaker>=1.1:www/py-beaker \
		${PYTHON_PKGNAMEPREFIX}MarkupSafe>=0.9.2:textproc/py-MarkupSafe \
		${PYTHON_PKGNAMEPREFIX}Babel>0:devel/py-babel
RUN_DEPENDS+=	${BUILD_DEPENDS}

USES=		python
USE_PYTHON=	autoplist distutils

.include <bsd.port.mk>
