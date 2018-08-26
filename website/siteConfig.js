// See https://docusaurus.io/docs/site-config.html for all the possible
// site configuration options.

/**
 * List of projects/orgs using your project for the users page
 */
const users = [
  /*
  TODO Let search for some users and add them here
  {
    caption: 'User1',
    // You will need to prepend the image path with your baseUrl
    // if it is not '/', like: '/test-site/img/docusaurus.svg'.
    image: '/img/docusaurus.svg',
    infoLink: 'https://www.facebook.com',
    pinned: true,
  },
  */
];

const siteConfig = {
  title: 'LANSuite',
  tagline: 'A Content Management System designed especially for the needs of LAN-Parties',
  url: 'https://lansuite.github.io',
  baseUrl: '/lansuite/',

  // Used for publishing and more
  projectName: 'lansuite',
  organizationName: 'lansuite',

  headerLinks: [
    {doc: 'installation', label: 'Docs'},
    // {doc: 'doc4', label: 'API'},
    {page: 'credits', label: 'Credits'},
    {blog: true, label: 'Blog'},
  ],

  // If you have users set above, you add it here
  users,

  // Path to images for header/footer
  headerIcon: 'img/docusaurus.svg',
  footerIcon: 'img/docusaurus.svg',
  favicon: 'img/favicon.png',

  // Colors for website
  colors: {
    primaryColor: '#2E8555',
    secondaryColor: '#205C3B',
  },

  // This copyright info is used in /core/Footer.js and blog rss/atom feeds.
  copyright:
    'Copyright © ' +
    new Date().getFullYear() +
    ' LANSuite team and contributors',

  highlight: {
    // Highlight.js theme to use for syntax highlighting in code blocks
    theme: 'default',
  },

  // Add custom scripts here that would be placed in <script> tags
  scripts: ['https://buttons.github.io/buttons.js'],

  // On page navigation for the current documentation page/
  onPageNav: 'separate',

  // Open Graph and Twitter card images
  ogImage: 'img/docusaurus.png',
  twitterImage: 'img/docusaurus.png',
};

module.exports = siteConfig;
