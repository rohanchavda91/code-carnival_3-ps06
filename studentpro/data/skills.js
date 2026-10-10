/**
 * Data Definition: Lifelong Learner & Professional Skills
 * For adult learners, professionals, and curious self-starters.
 */
const SKILLS_CATALOG = {
  categories: [
    { id: 'all', name: 'All Domains', icon: '🌟' },
    { id: 'tech', name: 'Technology & Code', icon: '💻' },
    { id: 'languages', name: 'Spoken Languages', icon: '🗣️' },
    { id: 'design', name: 'Design & Creative', icon: '🎨' },
    { id: 'career', name: 'Career & Leadership', icon: '🚀' },
    { id: 'business', name: 'Business & Finance', icon: '📊' }
  ],

  skills: [
    // --- Technology & Code ---
    {
      id: 'python_mastery',
      name: 'Python for Beginners & Automation',
      category: 'tech',
      difficulty: 'Beginner to Intermediate',
      badge: 'High Demand',
      icon: '🐍',
      description: 'Master clean Python syntax, loops, data structures, scripting, and automation projects.',
      topics: ['Python Syntax & Variables', 'Lists, Dictionaries & Sets', 'Functions & Modular Code', 'File I/O & Automation Scripts', 'Error Handling & APIs']
    },
    {
      id: 'javascript_modern',
      name: 'Modern JavaScript (ES6+)',
      category: 'tech',
      difficulty: 'Beginner to Intermediate',
      badge: 'Essential Web',
      icon: '⚡',
      description: 'The foundation of interactive web apps: arrow functions, promises, async/await, and DOM manipulation.',
      topics: ['Variables & Scoping (let/const)', 'Functions & Higher-Order Methods', 'Arrays & Object Destructuring', 'DOM Manipulation & Events', 'Async/Await & Fetch API']
    },
    {
      id: 'fullstack_web',
      name: 'Full-Stack Web Development',
      category: 'tech',
      difficulty: 'Intermediate',
      badge: 'Project-Based',
      icon: '🌐',
      description: 'Build complete web apps from responsive HTML/CSS frontend to server APIs and database storage.',
      topics: ['Semantic HTML5 & Modern CSS', 'Client-side Interactivity', 'Backend Routing & API Design', 'Relational Databases (SQL)', 'Deployment & Security']
    },
    {
      id: 'ai_prompt_eng',
      name: 'AI & Prompt Engineering',
      category: 'tech',
      difficulty: 'All Levels',
      badge: 'Trending',
      icon: '🤖',
      description: 'Harness LLMs, multi-step prompt design, reasoning techniques, and AI tools for work.',
      topics: ['LLM Fundamentals & Tokenization', 'Zero-shot, Few-shot & Chain of Thought', 'System Prompts & Personas', 'AI Workflow Automation', 'Ethical AI & Hallucination Mitigation']
    },
    {
      id: 'sql_data_analytics',
      name: 'SQL & Database Queries',
      category: 'tech',
      difficulty: 'Beginner to Intermediate',
      badge: 'Data Career',
      icon: '🗄️',
      description: 'Query, filter, join, and analyze structured data like an analytics professional.',
      topics: ['SELECT, WHERE & Filtering', 'Aggregations & GROUP BY', 'JOINs (INNER, LEFT, FULL)', 'Subqueries & Window Functions', 'Database Schema Design']
    },
    {
      id: 'cybersec_basics',
      name: 'Cyber Security Essentials',
      category: 'tech',
      difficulty: 'Beginner',
      badge: 'Security',
      icon: '🛡️',
      description: 'Understand digital threats, password cryptography, web vulnerabilities, and safe system practices.',
      topics: ['Threat Landscape & Malware', 'Authentication & Encryption', 'Web Security (XSS, SQLi Basics)', 'Network Safety & Firewalls']
    },

    // --- Spoken Languages ---
    {
      id: 'english_spoken',
      name: 'Spoken English & Fluency',
      category: 'languages',
      difficulty: 'Beginner to Advanced',
      badge: 'Global Skill',
      icon: '🇬🇧',
      description: 'Gain natural conversational confidence, vocabulary expansion, and clean pronunciation.',
      topics: ['Everyday Conversational Phrases', 'Practical Grammar & Tenses', 'Vocabulary Building & Synonyms', 'Professional Email & Phone Etiquette', 'Accent Neutrality & Clarity']
    },
    {
      id: 'spanish_conversational',
      name: 'Conversational Spanish (Español)',
      category: 'languages',
      difficulty: 'Beginner (A1 - A2)',
      badge: 'Popular',
      icon: '🇪🇸',
      description: 'Start speaking Spanish from day one with daily greetings, travel survival phrases, and sentence patterns.',
      topics: ['Greetings, Introductions & Numbers', 'Ser vs Estar Verbs', 'Everyday Questions & Directions', 'Food, Travel & Culture', 'Present Tense Conjugations']
    },
    {
      id: 'french_essentials',
      name: 'French for Beginners (Français)',
      category: 'languages',
      difficulty: 'Beginner (A1)',
      badge: 'Culture & Travel',
      icon: '🇫🇷',
      description: 'Foundations of French pronunciation, key phrases, food ordering, and polite dialogue.',
      topics: ['French Phonics & Nasal Vowels', 'Basic Social Greetings', 'Articles & Gendered Nouns', 'Ordering at a Bistro', 'Common Verbs (Être, Avoir, Aller)']
    },
    {
      id: 'german_basics',
      name: 'German Language Foundations (Deutsch)',
      category: 'languages',
      difficulty: 'Beginner (A1)',
      badge: 'Study in Germany',
      icon: '🇩🇪',
      description: 'Sentence structures, the four grammatical cases made easy, and conversational practice.',
      topics: ['Alphabet & Accurate Sounds', 'Nominativ & Akkusativ Basics', 'Daily Routines & Telling Time', 'Directions & Shopping', 'Modal Verbs (Können, Müssen)']
    },
    {
      id: 'japanese_starter',
      name: 'Japanese for Beginners (日本語)',
      category: 'languages',
      difficulty: 'Beginner (JLPT N5)',
      badge: 'Cultural Track',
      icon: '🇯🇵',
      description: 'Hiragana, Katakana, basic grammar particles, and courteous everyday Japanese conversation.',
      topics: ['Hiragana & Katakana Masterclass', 'Essential Particles (Wa, Ga, O, Ni)', 'Desu / Masu Polite Forms', 'Self Introductions & Anime Phrases', 'Basic Kanji Intro']
    },

    // --- Design & Creative ---
    {
      id: 'uiux_figma',
      name: 'UI/UX Design with Figma',
      category: 'design',
      difficulty: 'Beginner to Intermediate',
      badge: 'High Value',
      icon: '🎨',
      description: 'Design digital products, wireframes, component systems, and interactive click-through prototypes.',
      topics: ['Design Thinking & User Empathy', 'Layouts, Grids & Typography', 'Figma Auto-Layout & Components', 'Prototyping & Micro-interactions', 'Design Handoff to Developers']
    },
    {
      id: 'graphic_design',
      name: 'Graphic Design & Visual Branding',
      category: 'design',
      difficulty: 'Beginner',
      badge: 'Creative',
      icon: '🖼️',
      description: 'Color theory, composition rules, logo concepts, and visual storytelling for marketing.',
      topics: ['Color Psychology & Harmony', 'Typography Pairing & Hierarchy', 'Logo Design Fundamentals', 'Social Media Asset Creation', 'Visual Balance & Contrast']
    },
    {
      id: 'video_editing',
      name: 'Video Editing & Visual Storytelling',
      category: 'design',
      difficulty: 'Beginner to Intermediate',
      badge: 'Content Creator',
      icon: '🎬',
      description: 'Pacing, cut techniques, audio leveling, motion graphics, and engaging social formats.',
      topics: ['Timeline Navigation & Cuts', 'J-Cuts, L-Cuts & Match Cuts', 'Audio Cleanup & Sound Design', 'Color Grading Basics', 'Short-form Reels & Shorts Flow']
    },

    // --- Career & Leadership ---
    {
      id: 'public_speaking',
      name: 'Public Speaking & Presentation Skills',
      category: 'career',
      difficulty: 'All Levels',
      badge: 'Confidence',
      icon: '🎙️',
      description: 'Overcome stage fear, structure memorable talks, hook audiences, and command the room.',
      topics: ['Managing Stage Anxiety', 'The 3-Act Speech Structure', 'Vocal Variety & Body Language', 'Slide Design that Doesn’t Bore', 'Handling Q&A Confidently']
    },
    {
      id: 'interview_prep',
      name: 'Tech & Corporate Interview Preparation',
      category: 'career',
      difficulty: 'Intermediate',
      badge: 'Career Boost',
      icon: '💼',
      description: 'Master the STAR method, technical behavioral rounds, salary negotiation, and resume storytelling.',
      topics: ['Resume & LinkedIn Profile Optimization', 'Behavioral Questions (STAR Technique)', 'System & Case Study Thinking', 'Salary Negotiation Tactics', 'Post-Interview Follow-Up']
    },
    {
      id: 'leadership_mgmt',
      name: 'Leadership & Team Management',
      category: 'career',
      difficulty: 'Intermediate',
      badge: 'Executive',
      icon: '🏆',
      description: 'Empower teams, deliver constructive feedback, resolve conflict, and execute strategic priorities.',
      topics: ['Situational Leadership Models', 'Radical Candor & Feedback Loops', 'Conflict Resolution Frameworks', 'Delegation & Trust Building', 'Goal Setting with OKRs']
    },

    // --- Business & Finance ---
    {
      id: 'digital_marketing',
      name: 'Digital Marketing & Growth Strategy',
      category: 'business',
      difficulty: 'Beginner to Intermediate',
      badge: 'Commercial',
      icon: '📈',
      description: 'Customer acquisition funnels, SEO, social ad campaigns, content marketing, and metrics.',
      topics: ['Customer Personas & Acquisition Funnels', 'Search Engine Optimization (SEO)', 'Content & Email Marketing', 'Paid Ads & ROI Metrics', 'Conversion Rate Optimization']
    },
    {
      id: 'personal_finance',
      name: 'Personal Finance & Wealth Building',
      category: 'business',
      difficulty: 'All Levels',
      badge: 'Life Essential',
      icon: '💰',
      description: 'Budgeting systems, compound interest, index funds, asset allocation, and debt freedom.',
      topics: ['Cashflow Budgeting (50/30/20 Rule)', 'The Power of Compounding & Inflation', 'Index Funds & Mutual Funds Basics', 'Emergency Funds & Risk Hedging', 'Tax Planning Fundamentals']
    }
  ]
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { SKILLS_CATALOG };
}
