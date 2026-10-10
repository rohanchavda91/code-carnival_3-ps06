/**
 * Data Definition: College & Higher Education Catalog
 * Scalable taxonomy across degree levels (UG, PG, Diploma, Certifications) and domains.
 */
const COLLEGE_CATALOG = {
  levels: [
    { id: 'all', name: 'All Levels' },
    { id: 'ug', name: 'Undergraduate (UG)' },
    { id: 'pg', name: 'Postgraduate (PG)' },
    { id: 'diploma', name: 'Diploma & Vocational' },
    { id: 'cert', name: 'Certifications & Specializations' }
  ],

  categories: [
    { id: 'all', name: 'All Disciplines', icon: '🎓' },
    { id: 'cs_it', name: 'Computer Science & IT', icon: '💻' },
    { id: 'comm_mgmt', name: 'Commerce & Management', icon: '📈' },
    { id: 'science', name: 'Sciences & Research', icon: '🔬' },
    { id: 'arts_humanities', name: 'Arts & Humanities', icon: '🏛️' },
    { id: 'professional', name: 'Professional & Design', icon: '⚖️' }
  ],

  courses: [
    // --- Computer Science & IT ---
    {
      id: 'bca',
      code: 'BCA',
      name: 'Bachelor of Computer Applications',
      level: 'ug',
      category: 'cs_it',
      duration: '3 Years',
      icon: '💻',
      description: 'Foundations of computer systems, programming, web applications, and database management.',
      topics: ['JavaScript & Web Tech', 'Data Structures in C++', 'Database Management Systems (SQL)', 'Object-Oriented Programming (Java)', 'Operating Systems', 'Software Engineering']
    },
    {
      id: 'btech_cse',
      code: 'B.Tech CSE',
      name: 'B.Tech in Computer Science & Engineering',
      level: 'ug',
      category: 'cs_it',
      duration: '4 Years',
      icon: '⚙️',
      description: 'Comprehensive software engineering, computer architecture, algorithms, and distributed systems.',
      topics: ['Algorithms & Complexity', 'Computer Architecture', 'Compiler Design', 'Distributed Systems', 'Computer Networks', 'Cloud Computing']
    },
    {
      id: 'bsc_cs',
      code: 'B.Sc CS',
      name: 'B.Sc in Computer Science',
      level: 'ug',
      category: 'cs_it',
      duration: '3 Years',
      icon: '🖥️',
      description: 'Theoretical foundations of computational logic, discrete mathematics, and systems design.',
      topics: ['Discrete Mathematics', 'C/C++ Systems Programming', 'Data Structures', 'Linux Architecture', 'Theory of Computation']
    },
    {
      id: 'mca',
      code: 'MCA',
      name: 'Master of Computer Applications',
      level: 'pg',
      category: 'cs_it',
      duration: '2 Years',
      icon: '🧠',
      description: 'Advanced software enterprise engineering, cloud platforms, and microservices architecture.',
      topics: ['Enterprise Java & Spring Boot', 'Cloud Architecture (AWS/GCP)', 'Advanced DBMS', 'DevOps & CI/CD', 'Mobile App Development']
    },
    {
      id: 'msc_it',
      code: 'M.Sc IT',
      name: 'M.Sc in Information Technology',
      level: 'pg',
      category: 'cs_it',
      duration: '2 Years',
      icon: '🌐',
      description: 'Information security, enterprise networking, and modern business intelligence infrastructure.',
      topics: ['Information Security & Cryptography', 'Big Data Technologies', 'Network Engineering', 'Business Intelligence']
    },
    {
      id: 'data_science',
      code: 'B.Sc / Cert DS',
      name: 'Data Science & Artificial Intelligence',
      level: 'ug',
      category: 'cs_it',
      duration: '3 Years',
      icon: '📊',
      description: 'Mathematical modeling, predictive analytics, neural networks, and Python-driven machine learning.',
      topics: ['Python for Data Science', 'Applied Statistics & Probability', 'Machine Learning Algorithms', 'Deep Learning & Neural Nets', 'Data Visualization (PowerBI/Tableau)']
    },
    {
      id: 'cyber_security',
      code: 'B.Tech / Cert Sec',
      name: 'Cyber Security & Ethical Hacking',
      level: 'ug',
      category: 'cs_it',
      duration: '4 Years',
      icon: '🛡️',
      description: 'Defensive systems engineering, penetration testing, cryptography, and network forensics.',
      topics: ['Network Penetration Testing', 'Cryptography & Public Key Infra', 'Malware Analysis', 'Web Application Security (OWASP)', 'Digital Forensics']
    },
    {
      id: 'web_dev_diploma',
      code: 'Dip. WebDev',
      name: 'Diploma in Full-Stack Web Development',
      level: 'diploma',
      category: 'cs_it',
      duration: '1 Year',
      icon: '⚡',
      description: 'Modern front-end, back-end APIs, database modeling, and real-world deployment pipeline.',
      topics: ['HTML5, Modern CSS & Tailwind', 'JavaScript (ES6+) & TypeScript', 'Backend APIs (Node/Express/PHP)', 'PostgreSQL & MongoDB', 'Git, CI/CD & Cloud Hosting']
    },
    {
      id: 'ai_ml_cert',
      code: 'Cert. AI/ML',
      name: 'Specialization in Applied Generative AI & ML',
      level: 'cert',
      category: 'cs_it',
      duration: '6 Months',
      icon: '🤖',
      description: 'Large language models, prompt engineering, fine-tuning, RAG pipelines, and agentic workflows.',
      topics: ['Foundational LLM Concepts', 'Prompt Engineering & Reasoning', 'Vector Databases & RAG', 'LangChain & Agentic Systems', 'Model Deployment & APIs']
    },

    // --- Commerce & Management ---
    {
      id: 'bcom',
      code: 'B.Com (Hons)',
      name: 'Bachelor of Commerce',
      level: 'ug',
      category: 'comm_mgmt',
      duration: '3 Years',
      icon: '📈',
      description: 'Corporate accounting, income tax laws, corporate finance, and commercial audits.',
      topics: ['Financial Accounting', 'Corporate Law', 'Direct & Indirect Taxation (GST)', 'Cost Accounting', 'Auditing & Assurance']
    },
    {
      id: 'bba',
      code: 'BBA',
      name: 'Bachelor of Business Administration',
      level: 'ug',
      category: 'comm_mgmt',
      duration: '3 Years',
      icon: '💼',
      description: 'Organizational strategy, marketing funnels, operations management, and human capital.',
      topics: ['Principles of Management', 'Marketing Management', 'Financial Modeling', 'Organizational Behavior', 'Business Communication']
    },
    {
      id: 'mba',
      code: 'MBA',
      name: 'Master of Business Administration',
      level: 'pg',
      category: 'comm_mgmt',
      duration: '2 Years',
      icon: '👔',
      description: 'Strategic leadership, venture financing, market expansion, and executive decision-making.',
      topics: ['Strategic Management', 'Corporate Valuation & M&A', 'Global Supply Chain', 'Consumer Psychology', 'Executive Leadership']
    },
    {
      id: 'fin_analytics',
      code: 'M.Sc / Cert Fin',
      name: 'Financial Analytics & FinTech',
      level: 'pg',
      category: 'comm_mgmt',
      duration: '2 Years',
      icon: '💳',
      description: 'Algorithmic trading, blockchain mechanics, risk metrics, and quantitative finance.',
      topics: ['Quantitative Financial Modeling', 'Python for Finance', 'Risk Management & Basel Norms', 'FinTech & Blockchain Ecosystems']
    },
    {
      id: 'hr_management',
      code: 'PG Dip. HR',
      name: 'Human Resource Management & Talent Strategy',
      level: 'diploma',
      category: 'comm_mgmt',
      duration: '1 Year',
      icon: '🤝',
      description: 'Strategic workforce planning, performance appraisals, labor laws, and employee engagement.',
      topics: ['Talent Acquisition & Sourcing', 'Labor Laws & Industrial Relations', 'Performance Management Systems', 'HR Analytics']
    },

    // --- Sciences & Research ---
    {
      id: 'bsc_physics',
      code: 'B.Sc Physics',
      name: 'B.Sc in Physics',
      level: 'ug',
      category: 'science',
      duration: '3 Years',
      icon: '⚛️',
      description: 'Classical mechanics, quantum physics, thermodynamics, electromagnetism, and optics.',
      topics: ['Classical Mechanics', 'Quantum Mechanics', 'Electrodynamics', 'Thermodynamics & Statistical Physics', 'Nuclear & Particle Physics']
    },
    {
      id: 'bsc_chemistry',
      code: 'B.Sc Chemistry',
      name: 'B.Sc in Chemistry',
      level: 'ug',
      category: 'science',
      duration: '3 Years',
      icon: '🧪',
      description: 'Organic reaction mechanisms, physical kinetics, inorganic coordination, and spectroscopic analysis.',
      topics: ['Organic Reaction Mechanisms', 'Physical Chemistry Kinetics', 'Inorganic Coordination Chemistry', 'Spectroscopy (NMR, IR, UV)']
    },
    {
      id: 'bsc_math',
      code: 'B.Sc Math',
      name: 'B.Sc in Mathematics & Statistics',
      level: 'ug',
      category: 'science',
      duration: '3 Years',
      icon: '📐',
      description: 'Real analysis, linear algebra, abstract algebra, multivariable calculus, and probability theory.',
      topics: ['Real Analysis', 'Linear Algebra & Matrices', 'Differential Equations', 'Abstract Group Theory', 'Complex Analysis']
    },
    {
      id: 'biotech',
      code: 'B.Sc / M.Sc Biotech',
      name: 'Biotechnology & Bioinformatics',
      level: 'ug',
      category: 'science',
      duration: '3 Years',
      icon: '🧬',
      description: 'Genetic engineering, molecular biology, fermentation tech, and computational genomics.',
      topics: ['Recombinant DNA Technology', 'Molecular Biology & Genetics', 'Immunology & Diagnostics', 'Bioinformatics & Sequence Analysis']
    },
    {
      id: 'microbio',
      code: 'B.Sc Microbio',
      name: 'Microbiology & Clinical Research',
      level: 'ug',
      category: 'science',
      duration: '3 Years',
      icon: '🧫',
      description: 'Bacterial physiology, virology, immunology, and industrial fermentation.',
      topics: ['Medical Microbiology', 'Virology & Mycology', 'Environmental Microbiology', 'Bioprocess Engineering']
    },

    // --- Arts & Humanities ---
    {
      id: 'ba_psychology',
      code: 'BA Psych',
      name: 'BA in Psychology & Behavioral Science',
      level: 'ug',
      category: 'arts_humanities',
      duration: '3 Years',
      icon: '🧠',
      description: 'Cognitive processes, developmental milestones, abnormal psychology, and psychometric testing.',
      topics: ['Cognitive Psychology', 'Social Psychology', 'Abnormal Psychology & Diagnostics', 'Research Methods & Statistics', 'Counselling Foundations']
    },
    {
      id: 'ba_sociology',
      code: 'BA Sociology',
      name: 'BA in Sociology & Social Policy',
      level: 'ug',
      category: 'arts_humanities',
      duration: '3 Years',
      icon: '👥',
      description: 'Social theory, demographic trends, urban sociology, gender studies, and societal stratification.',
      topics: ['Classical Sociological Thinkers', 'Indian Social Institutions', 'Urban & Rural Sociology', 'Gender, Caste & Class Dynamics']
    },
    {
      id: 'ba_economics',
      code: 'BA Eco (Hons)',
      name: 'BA in Economics',
      level: 'ug',
      category: 'arts_humanities',
      duration: '3 Years',
      icon: '📊',
      description: 'Advanced microeconomic equilibrium, econometric modeling, fiscal policy, and international trade.',
      topics: ['Microeconomic Analysis', 'Macroeconomic Policy', 'Introductory Econometrics', 'Development Economics', 'Public Finance']
    },
    {
      id: 'ba_pol_sci',
      code: 'BA Pol.Sci',
      name: 'BA in Political Science & International Relations',
      level: 'ug',
      category: 'arts_humanities',
      duration: '3 Years',
      icon: '🏛️',
      description: 'Political theory, constitutional frameworks, geopolitics, and global diplomacy.',
      topics: ['Western Political Thought', 'Indian Political Thought', 'International Relations & Diplomacy', 'Comparative Governance']
    },

    // --- Professional & Design ---
    {
      id: 'llb_law',
      code: 'LLB / B.A. LLB',
      name: 'Bachelor of Laws (Jurisprudence)',
      level: 'ug',
      category: 'professional',
      duration: '3 - 5 Years',
      icon: '⚖️',
      description: 'Constitutional law, criminal jurisprudence, corporate contracts, civil procedure, and torts.',
      topics: ['Constitutional Law of India', 'Law of Contracts & Specific Relief', 'Indian Penal Code & Criminal Justice', 'Corporate Law & Intellectual Property']
    },
    {
      id: 'bdes_uiux',
      code: 'B.Des UI/UX',
      name: 'Bachelor of Design (Interaction & UI/UX)',
      level: 'ug',
      category: 'professional',
      duration: '4 Years',
      icon: '🎨',
      description: 'Design thinking, wireframing, heuristic evaluation, design systems, and usability testing.',
      topics: ['Design Thinking & User Research', 'Information Architecture & Wireframing', 'UI Design Systems (Figma)', 'Interaction Prototyping & Motion', 'Usability Auditing']
    },
    {
      id: 'bpharm',
      code: 'B.Pharm',
      name: 'Bachelor of Pharmacy',
      level: 'ug',
      category: 'professional',
      duration: '4 Years',
      icon: '💊',
      description: 'Pharmaceutical chemistry, pharmacology, dosage formulation, and drug regulatory affairs.',
      topics: ['Pharmaceutics & Formulation', 'Medicinal Chemistry', 'Pharmacology & Toxicology', 'Pharmacognosy & Phytochemistry']
    },
    {
      id: 'barch',
      code: 'B.Arch',
      name: 'Bachelor of Architecture & Spatial Design',
      level: 'ug',
      category: 'professional',
      duration: '5 Years',
      icon: '📐',
      description: 'Architectural drafting, structural systems, sustainable urban design, and building materials.',
      topics: ['Architectural Drawing & Graphics', 'Building Construction & Materials', 'History of World Architecture', 'Sustainable Environmental Design']
    }
  ]
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { COLLEGE_CATALOG };
}
