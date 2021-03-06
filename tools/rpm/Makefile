RPM_TMP=$(HOME)/rpmbuild
PHP_BASE=php
BASE_DIR=$(shell cd ../..; pwd)

version=$(shell LANG=C cat $(BASE_DIR)/VERSION)

CORE_MAILMAN_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/CORE_MAILMAN_VERSION)
CORE_SUBVERSION_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/CORE_SUBVERSION_VERSION)
CORE_CVS_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/CORE_CVS_VERSION)
CORE_REST_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/api/VERSION)

PLUGIN_FORUMML_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/forumml/VERSION)
PLUGIN_GIT_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/git/VERSION)
PLUGIN_SVNTODIMENSIONS_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/svntodimensions/VERSION)
PLUGIN_CVSTODIMENSIONS_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/cvstodimensions/VERSION)
PLUGIN_DOCMANWATERMARK_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/docmanwatermark/VERSION)
PLUGIN_LDAP_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/ldap/VERSION)
PLUGIN_IM_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/IM/VERSION)
PLUGIN_JRI_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/codendijri/VERSION)
PLUGIN_ECLIPSE_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/eclipse/VERSION)
PLUGIN_HUDSON_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/hudson/VERSION)
PLUGIN_WEBDAV_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/webdav/VERSION)
PLUGIN_REQUESTHELP_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/requesthelp/VERSION)
PLUGIN_TRACKER_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/tracker/VERSION)
PLUGIN_GRAPHONTRACKERS_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/graphontrackersv5/VERSION)
PLUGIN_CARDWALL_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/cardwall/VERSION)
PLUGIN_AGILEDASHBOARD_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/agiledashboard/VERSION)
PLUGIN_FULLTEXTSEARCH_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/fulltextsearch/VERSION)
PLUGIN_ARCHIVEDELETEDITEMS_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/archivedeleteditems/VERSION)
PLUGIN_FUSIONFORGE_COMPAT_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/fusionforge_compat/VERSION)
PLUGIN_ADMSSW_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/admssw/VERSION)
PLUGIN_DOAPRDF_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/doaprdf/VERSION)
PLUGIN_FOAFPROFILES_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/foafprofiles/VERSION)
PLUGIN_MEDIAWIKI_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/mediawiki/VERSION)
PLUGIN_BOOMERANG_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/boomerang/VERSION)
PLUGIN_OPENID_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/openid/VERSION)
PLUGIN_PROFTPD_VERSION=$(shell LANG=C cat $(BASE_DIR)/plugins/proftpd/VERSION)

THEME_DAWN_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/themes/Dawn/VERSION)
THEME_SAVANNAH_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/themes/savannah/VERSION)
THEME_STTAB_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/themes/STTab/VERSION)
THEME_CODEXSTN_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/themes/CodexSTN/VERSION)
THEME_STEERFORGE_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/themes/SteerForge/VERSION)
THEME_TULEAP_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/themes/Tuleap/VERSION)
THEME_EXPERIMENTAL_VERSION=$(shell LANG=C cat $(BASE_DIR)/src/www/themes/Experimental/VERSION)

ifdef GIT_BRANCH
TAG_RELEASE = $(subst -, ,$(GIT_BRANCH))
RELEASE = $(word 2, $(TAG_RELEASE))
endif

ifeq ($(strip $(RELEASE)),)
RELEASE=1
endif

PKG_NAME=tuleap

ifeq ($(strip $(OS)),rhel6)
SPEC_FILE=tuleap.rhel6.spec
CUSTOM_SPEC_FILE=customization.rhel6.spec
else
SPEC_FILE=tuleap.spec
CUSTOM_SPEC_FILE=codendi-customization.spec
endif

default: all

all: clean rpmprep rpm custom_rpm rpm-all-deps tuleap-all

##
## Setup environment
##

rpmprep: $(RPM_TMP) ~/.rpmmacros

~/.rpmmacros:
	@echo "Creating ~/.rpmmacros"
	@echo "%_topdir $(RPM_TMP)" > ~/.rpmmacros
	@echo '%_tmppath %{_topdir}/TMP' >> ~/.rpmmacros
	@echo '%_buildroot %{_tmppath}/%{name}-root' >> ~/.rpmmacros
	@echo '%_sysconfdir /etc' >> ~/.rpmmacros
	@echo '%php_base $(PHP_BASE)' >> ~/.rpmmacros

$(RPM_TMP):
	@[ -d $@ ] || mkdir -p $@ $@/BUILD $@/RPMS $@/SOURCES $@/SPECS $@/SRPMS $@/TMP

##
## Build tarball
##

tarball: rpmprep $(RPM_TMP)/SOURCES/$(PKG_NAME)-$(version).tar.gz

less:
	$(MAKE) -C ../.. less

$(RPM_TMP)/SOURCES/$(PKG_NAME)-$(version).tar.gz: less
	[ -h $(RPM_TMP)/SOURCES/$(PKG_NAME)-$(version) ] || ln -s $(BASE_DIR) $(RPM_TMP)/SOURCES/$(PKG_NAME)-$(version)
	cd $(RPM_TMP)/SOURCES && \
		find $(PKG_NAME)-$(version)/ \(\
			 -path '*/.svn' -o\
			 -name '*.less' -o\
			 -name ".git" -o\
			 -name ".gitignore" -o\
			 -name ".gitmodules" -o\
			 -path "*/.DS_Store" -o\
			 -path $(PKG_NAME)-$(version)/plugins/tests -o\
			 -path $(PKG_NAME)-$(version)/plugins/maillog \)\
			-prune -o -print |\
		 cpio -o -H ustar --quiet |\
		 gzip > $(RPM_TMP)/SOURCES/$(PKG_NAME)-$(version).tar.gz

##
## Build RPM
##

rpm: tarball $(RPM_TMP)/RPMS/noarch/$(PKG_NAME)-$(version)-$(RELEASE).noarch.rpm

$(RPM_TMP)/RPMS/noarch/$(PKG_NAME)-$(version)-$(RELEASE).noarch.rpm: $(SPEC_FILE)
	cat $(SPEC_FILE) |\
		sed -e 's/@@VERSION@@/$(version)/g' |\
		sed -e 's/@@RELEASE@@/$(RELEASE)/g' |\
		sed -e 's/@@CORE_MAILMAN_VERSION@@/$(CORE_MAILMAN_VERSION)/g' |\
		sed -e 's/@@CORE_SUBVERSION_VERSION@@/$(CORE_SUBVERSION_VERSION)/g' |\
		sed -e 's/@@CORE_CVS_VERSION@@/$(CORE_CVS_VERSION)/g' |\
		sed -e 's/@@CORE_REST_VERSION@@/$(CORE_REST_VERSION)/g' |\
		sed -e 's/@@PLUGIN_FORUMML_VERSION@@/$(PLUGIN_FORUMML_VERSION)/g' |\
		sed -e 's/@@PLUGIN_GIT_VERSION@@/$(PLUGIN_GIT_VERSION)/g' |\
		sed -e 's/@@PLUGIN_SVNTODIMENSIONS_VERSION@@/$(PLUGIN_SVNTODIMENSIONS_VERSION)/g' |\
		sed -e 's/@@PLUGIN_CVSTODIMENSIONS_VERSION@@/$(PLUGIN_CVSTODIMENSIONS_VERSION)/g' |\
		sed -e 's/@@PLUGIN_DOCMANWATERMARK_VERSION@@/$(PLUGIN_DOCMANWATERMARK_VERSION)/g' |\
		sed -e 's/@@PLUGIN_LDAP_VERSION@@/$(PLUGIN_LDAP_VERSION)/g' |\
		sed -e 's/@@PLUGIN_IM_VERSION@@/$(PLUGIN_IM_VERSION)/g' |\
		sed -e 's/@@PLUGIN_JRI_VERSION@@/$(PLUGIN_JRI_VERSION)/g' |\
		sed -e 's/@@PLUGIN_ECLIPSE_VERSION@@/$(PLUGIN_ECLIPSE_VERSION)/g' |\
		sed -e 's/@@PLUGIN_HUDSON_VERSION@@/$(PLUGIN_HUDSON_VERSION)/g' |\
		sed -e 's/@@PLUGIN_WEBDAV_VERSION@@/$(PLUGIN_WEBDAV_VERSION)/g' |\
		sed -e 's/@@PLUGIN_REQUESTHELP_VERSION@@/$(PLUGIN_REQUESTHELP_VERSION)/g' |\
		sed -e 's/@@PLUGIN_TRACKER_VERSION@@/$(PLUGIN_TRACKER_VERSION)/g' |\
		sed -e 's/@@PLUGIN_GRAPHONTRACKERS_VERSION@@/$(PLUGIN_GRAPHONTRACKERS_VERSION)/g' |\
		sed -e 's/@@PLUGIN_CARDWALL_VERSION@@/$(PLUGIN_CARDWALL_VERSION)/g' |\
		sed -e 's/@@PLUGIN_AGILEDASHBOARD_VERSION@@/$(PLUGIN_AGILEDASHBOARD_VERSION)/g' |\
		sed -e 's/@@PLUGIN_FULLTEXTSEARCH_VERSION@@/$(PLUGIN_FULLTEXTSEARCH_VERSION)/g' |\
		sed -e 's/@@PLUGIN_ARCHIVEDELETEDITEMS_VERSION@@/$(PLUGIN_ARCHIVEDELETEDITEMS_VERSION)/g' |\
		sed -e 's/@@PLUGIN_FUSIONFORGE_COMPAT_VERSION@@/$(PLUGIN_FUSIONFORGE_COMPAT_VERSION)/g' |\
		sed -e 's/@@PLUGIN_ADMSSW_VERSION@@/$(PLUGIN_ADMSSW_VERSION)/g' |\
		sed -e 's/@@PLUGIN_BOOMERANG_VERSION@@/$(PLUGIN_BOOMERANG_VERSION)/g' |\
		sed -e 's/@@PLUGIN_DOAPRDF_VERSION@@/$(PLUGIN_DOAPRDF_VERSION)/g' |\
		sed -e 's/@@PLUGIN_FOAFPROFILES_VERSION@@/$(PLUGIN_FOAFPROFILES_VERSION)/g' |\
		sed -e 's/@@PLUGIN_MEDIAWIKI_VERSION@@/$(PLUGIN_MEDIAWIKI_VERSION)/g' |\
		sed -e 's/@@PLUGIN_OPENID_VERSION@@/$(PLUGIN_OPENID_VERSION)/g' |\
		sed -e 's/@@PLUGIN_PROFTPD_VERSION@@/$(PLUGIN_PROFTPD_VERSION)/g' |\
		sed -e 's/@@THEME_DAWN_VERSION@@/$(THEME_DAWN_VERSION)/g' |\
		sed -e 's/@@THEME_SAVANNAH_VERSION@@/$(THEME_SAVANNAH_VERSION)/g' |\
		sed -e 's/@@THEME_STTAB_VERSION@@/$(THEME_STTAB_VERSION)/g' |\
		sed -e 's/@@THEME_CODEXSTN_VERSION@@/$(THEME_CODEXSTN_VERSION)/g' |\
		sed -e 's/@@THEME_STEERFORGE_VERSION@@/$(THEME_STEERFORGE_VERSION)/g' |\
		sed -e 's/@@THEME_TULEAP_VERSION@@/$(THEME_TULEAP_VERSION)/g' |\
		sed -e 's/@@THEME_EXPERIMENTAL_VERSION@@/$(THEME_EXPERIMENTAL_VERSION)/g' |\
		sed -e 's/@@PKG_NAME@@/$(PKG_NAME)/g' \
		> $(RPM_TMP)/SPECS/$(SPEC_FILE)
	rpmbuild -bb $(RPM_TMP)/SPECS/$(SPEC_FILE)

custom_rpm: tarball
	cat $(CUSTOM_SPEC_FILE) |\
		sed -e 's/@@PLATFORM@@/default/' |\
		sed -e 's/@@SYS_DEFAULT_DOMAIN@@/tuleap.net/' |\
		sed -e 's/@@SYS_HTTPS_HOST@@/tuleap.net/' |\
		sed -e 's/@@VERSION@@/$(version)/g' |\
		sed -e 's/@@PKG_NAME@@/$(PKG_NAME)/g' \
		> $(RPM_TMP)/SPECS/codendi-customization.spec
	cp *_ParametersLocal.dtd $(RPM_TMP)/SOURCES/
	cp $(BASE_DIR)/src/www/themes/common/images/organization_logo.png $(RPM_TMP)/SOURCES/organization_logo.png
	export DOCBOOK_TOOLS_DIR=$(DOCBOOK_TOOLS_DIR); rpmbuild -bb $(RPM_TMP)/SPECS/codendi-customization.spec

rpm-all-deps: codendi-all-deps.spec
	cat $< |\
		sed -e 's/@@VERSION@@/$(version)/g' |\
		sed -e 's/@@PKG_NAME@@/$(PKG_NAME)/g' \
		> $(RPM_TMP)/SPECS/$<
	cp README.all-deps $(RPM_TMP)/SOURCES/
	rpmbuild -bb $(RPM_TMP)/SPECS/$<

tuleap-all: tuleap-all.spec
	cat $< |\
		sed -e 's/@@VERSION@@/$(version)/g' |\
		sed -e 's/@@PKG_NAME@@/$(PKG_NAME)/g' \
		> $(RPM_TMP)/SPECS/$<
	cp README.all $(RPM_TMP)/SOURCES/
	rpmbuild -bb $(RPM_TMP)/SPECS/$<

clean:
	-rm -Rf $(RPM_TMP)/*
	@[ ! -d $(RPM_TMP) ] || rmdir $(RPM_TMP)
	-rm -Rf ~/.rpmmacros

dist: 
	mkdir -p $(RPM_TMP)/yum
	cp -ar $(RPM_TMP)/RPMS/* $(RPM_TMP)/yum
	createrepo $(RPM_TMP)/yum

build-depends: /usr/bin/rpmbuild /usr/bin/createrepo /usr/bin/zip $(DOCBOOK_TOOLS_DIR)

/usr/bin/rpmbuild:
	yum -y install rpm-build

/usr/bin/createrepo:
	yum -y install createrepo

/usr/bin/zip:
	yum -y install zip

$(DOCBOOK_TOOLS_DIR):
	@echo "Using DOCBOOK_TOOLS_DIR=$@"
	@[ ! -d $@ ] && echo "DOCBOOK_TOOLS_DIR is missing, I suggest you take tarball from $(DOCBOOKTARBALL)" ; exit 1

# Legacy naming
codendi: rpm
custom_codendi: custom_rpm
codendi-all-deps: rpm-all-deps
