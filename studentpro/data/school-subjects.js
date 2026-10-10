/**
 * Data Definition: School Curriculum (Standard 1 - 12)
 * Grade-appropriate subjects and 11-12 stream mapping.
 */
const SCHOOL_CURRICULUM = {
  grades: [
    { standard: 1, name: 'Standard 1', tier: 'primary', description: 'Early foundations in numbers, language, and nature' },
    { standard: 2, name: 'Standard 2', tier: 'primary', description: 'Building fundamental literacy and basic arithmetic' },
    { standard: 3, name: 'Standard 3', tier: 'primary', description: 'Developing reading comprehension, problem-solving, and environmental awareness' },
    { standard: 4, name: 'Standard 4', tier: 'primary', description: 'Strengthening scientific observation and logical math' },
    { standard: 5, name: 'Standard 5', tier: 'middle', description: 'Introduction to structured general science and social concepts' },
    { standard: 6, name: 'Standard 6', tier: 'middle', description: 'Deeper science foundations, geometry, and world geography' },
    { standard: 7, name: 'Standard 7', tier: 'middle', description: 'Algebraic thinking, living systems, and civics' },
    { standard: 8, name: 'Standard 8', tier: 'middle', description: 'Advanced algebra basics, chemical changes, and historical eras' },
    { standard: 9, name: 'Standard 9', tier: 'secondary', description: 'Secondary board foundation: motion, atoms, polynomials, and constitution' },
    { standard: 10, name: 'Standard 10', tier: 'secondary', description: 'Board examination prep: calculus foundations, electricity, and social systems' },
    { standard: 11, name: 'Standard 11', tier: 'higher_secondary', description: 'Senior secondary stream specialization (Science, Commerce, Arts)' },
    { standard: 12, name: 'Standard 12', tier: 'higher_secondary', description: 'Senior secondary board completion and competitive entrance focus' }
  ],

  // Subjects mapped by standard (1-10)
  subjectsByStandard: {
    1: [
      { id: 'math_s1', name: 'Mathematics', code: 'MATH-1', icon: '🔢', topics: ['Counting 1-100', 'Basic Addition & Subtraction', 'Shapes & Patterns', 'Measurement Basics'] },
      { id: 'eng_s1', name: 'English', code: 'ENG-1', icon: '📖', topics: ['Phonics & Letters', 'Word Families', 'Simple Sentences', 'Story Listening'] },
      { id: 'evs_s1', name: 'Environmental Studies', code: 'EVS-1', icon: '🌱', topics: ['My Body & Family', 'Animals & Plants', 'Good Habits', 'Our Neighborhood'] },
      { id: 'hi_s1', name: 'Hindi', code: 'HIN-1', icon: '🇮🇳', topics: ['Varnamala', 'Matras Basics', 'Simple Words'] },
      { id: 'gu_s1', name: 'Gujarati', code: 'GUJ-1', icon: '🇮🇳', topics: ['Kakka & Barakhadi', 'Basic Vocabulary', 'Rhymes'] }
    ],
    2: [
      { id: 'math_s2', name: 'Mathematics', code: 'MATH-2', icon: '🔢', topics: ['2-digit Addition & Subtraction', 'Multiplication Tables (1-5)', 'Money & Coins', 'Time & Clock'] },
      { id: 'eng_s2', name: 'English', code: 'ENG-2', icon: '📖', topics: ['Nouns & Pronouns', 'Action Words', 'Short Comprehension', 'Spelling Rules'] },
      { id: 'evs_s2', name: 'Environmental Studies', code: 'EVS-2', icon: '🌱', topics: ['Seasons & Weather', 'Food We Eat', 'Water Sources', 'Safety Rules'] },
      { id: 'hi_s2', name: 'Hindi', code: 'HIN-2', icon: '🇮🇳', topics: ['Shabd Gyan', 'Vakya Rachna', 'Kahani'] },
      { id: 'gu_s2', name: 'Gujarati', code: 'GUJ-2', icon: '🇮🇳', topics: ['Shabd Rachna', 'Vartao', 'Balgeet'] }
    ],
    3: [
      { id: 'math_s3', name: 'Mathematics', code: 'MATH-3', icon: '🔢', topics: ['3-digit Operations', 'Multiplication & Division', 'Fractions Intro', 'Geometry Basics'] },
      { id: 'sci_s3', name: 'General Science', code: 'SCI-3', icon: '🔬', topics: ['Living vs Non-Living', 'Plant Parts', 'Birds & Animals', 'Air & Water'] },
      { id: 'eng_s3', name: 'English', code: 'ENG-3', icon: '📖', topics: ['Adjectives & Adverbs', 'Prepositions', 'Reading Stories', 'Paragraph Writing'] },
      { id: 'sst_s3', name: 'Social Studies', code: 'SST-3', icon: '🌍', topics: ['Our Earth', 'Continents & Oceans', 'Festivals of India', 'Occupations'] },
      { id: 'lang_s3', name: 'Regional Language (Hindi / Gujarati)', code: 'LANG-3', icon: '🗣️', topics: ['Grammar Basics', 'Poetry', 'Conversations'] }
    ],
    4: [
      { id: 'math_s4', name: 'Mathematics', code: 'MATH-4', icon: '🔢', topics: ['4-digit Arithmetic', 'Factors & Multiples', 'Fractions & Decimals', 'Perimeter & Area Intro'] },
      { id: 'sci_s4', name: 'Science', code: 'SCI-4', icon: '🔬', topics: ['Human Digestive System', 'States of Matter', 'Force & Simple Machines', 'Ecosystems'] },
      { id: 'eng_s4', name: 'English', code: 'ENG-4', icon: '📖', topics: ['Tenses (Past, Present, Future)', 'Conjunctions', 'Creative Writing', 'Poetry'] },
      { id: 'sst_s4', name: 'Social Studies', code: 'SST-4', icon: '🌍', topics: ['Physical Features of India', 'Climate & Forests', 'Ancient Civilizations', 'Civic Amenities'] },
      { id: 'cs_s4', name: 'Computer Basics', code: 'CS-4', icon: '💻', topics: ['Hardware & Software', 'Paint & Typing', 'Internet Safety'] }
    ],
    5: [
      { id: 'math_s5', name: 'Mathematics', code: 'MATH-5', icon: '🔢', topics: ['Large Numbers', 'HCF & LCM', 'Fractions & Decimals', 'Percentages & Angles'] },
      { id: 'sci_s5', name: 'Science', code: 'SCI-5', icon: '🔬', topics: ['Human Skeleton & Muscles', 'Nutrition & Diseases', 'Solar System', 'Natural Disasters'] },
      { id: 'eng_s5', name: 'English', code: 'ENG-5', icon: '📖', topics: ['Active & Passive Voice Intro', 'Idioms & Proverbs', 'Formal Letters', 'Essay Writing'] },
      { id: 'sst_s5', name: 'Social Studies', code: 'SST-5', icon: '🌍', topics: ['Freedom Movement', 'Indian Constitution Intro', 'World Climatic Zones', 'Maps & Globes'] },
      { id: 'cs_s5', name: 'Computer Basics', code: 'CS-5', icon: '💻', topics: ['Word Processing', 'Scratch Coding Basics', 'File Management'] }
    ],
    6: [
      { id: 'math_s6', name: 'Mathematics', code: 'MATH-6', icon: '🔢', topics: ['Integers', 'Algebra Intro', 'Ratio & Proportion', 'Basic Geometry (Lines & Angles)'] },
      { id: 'sci_s6', name: 'Science', code: 'SCI-6', icon: '🔬', topics: ['Food Components', 'Fibre to Fabric', 'Sorting Materials', 'Motion & Distances', 'Light & Shadows'] },
      { id: 'sst_s6', name: 'Social Science', code: 'SST-6', icon: '🌍', topics: ['Early Empires', 'Globe & Latitude/Longitude', 'Local Government', 'Diversity & Discrimination'] },
      { id: 'eng_s6', name: 'English', code: 'ENG-6', icon: '📖', topics: ['Grammar Mechanics', 'Comprehension Analysis', 'Notice Writing', 'Literature'] },
      { id: 'cs_s6', name: 'Computer Science', code: 'CS-6', icon: '💻', topics: ['Spreadsheets', 'Presentation Tools', 'Intro to Algorithms'] }
    ],
    7: [
      { id: 'math_s7', name: 'Mathematics', code: 'MATH-7', icon: '🔢', topics: ['Rational Numbers', 'Simple Equations', 'Lines & Angles', 'Triangles & Properties', 'Exponents & Powers'] },
      { id: 'sci_s7', name: 'Science', code: 'SCI-7', icon: '🔬', topics: ['Nutrition in Organisms', 'Heat & Temperature', 'Acids, Bases & Salts', 'Physical & Chemical Changes', 'Respiration'] },
      { id: 'sst_s7', name: 'Social Science', code: 'SST-7', icon: '🌍', topics: ['Medieval History', 'Our Environment', 'Role of State Government', 'Gender & Media'] },
      { id: 'eng_s7', name: 'English', code: 'ENG-7', icon: '📖', topics: ['Reported Speech', 'Clauses', 'Descriptive Composition', 'Poetic Devices'] },
      { id: 'cs_s7', name: 'Computer Science', code: 'CS-7', icon: '💻', topics: ['HTML Basics', 'Cyber Safety', 'Flowcharts & Logic'] }
    ],
    8: [
      { id: 'math_s8', name: 'Mathematics', code: 'MATH-8', icon: '🔢', topics: ['Rational Numbers', 'Linear Equations in 1 Variable', 'Understanding Quadrilaterals', 'Algebraic Identities', 'Mensuration'] },
      { id: 'sci_s8', name: 'Science', code: 'SCI-8', icon: '🔬', topics: ['Crop Production', 'Microorganisms', 'Synthetic Materials', 'Coal & Petroleum', 'Combustion & Flame', 'Cell Structure'] },
      { id: 'sst_s8', name: 'Social Science', code: 'SST-8', icon: '🌍', topics: ['Modern Indian History', 'Resources & Land', 'The Indian Judiciary', 'Secularism & Marginalisation'] },
      { id: 'eng_s8', name: 'English', code: 'ENG-8', icon: '📖', topics: ['Advanced Grammar', 'Analytical Writing', 'Debate & Speeches', 'Prose Appreciation'] },
      { id: 'cs_s8', name: 'Computer Science', code: 'CS-8', icon: '💻', topics: ['Python Basics', 'App Development Intro', 'Database Concepts'] }
    ],
    9: [
      { id: 'math_s9', name: 'Mathematics', code: 'MATH-9', icon: '🔢', topics: ['Number Systems', 'Polynomials', 'Coordinate Geometry', 'Linear Equations in 2 Variables', 'Euclid Geometry', 'Triangles & Circles', 'Surface Areas & Volumes'] },
      { id: 'sci_s9', name: 'Science', code: 'SCI-9', icon: '🔬', topics: ['Matter in Our Surroundings', 'Atoms & Molecules', 'Cell: Fundamental Unit of Life', 'Tissues', 'Motion & Newton Laws', 'Gravitation', 'Work & Energy'] },
      { id: 'sst_s9', name: 'Social Science', code: 'SST-9', icon: '🌍', topics: ['French & Russian Revolutions', 'India: Size & Location', 'Drainage & Climate', 'What is Democracy?', 'Economics of Village Palampur'] },
      { id: 'eng_s9', name: 'English Language & Lit', code: 'ENG-9', icon: '📖', topics: ['Tenses & Modals', 'Diary Entry & Story Writing', 'Critical Literature Analysis', 'Integrated Grammar'] },
      { id: 'cs_s9', name: 'Information Technology', code: 'IT-9', icon: '💻', topics: ['Python Programming', 'Digital Documentation', 'Web Technologies', 'Green Skills'] }
    ],
    10: [
      { id: 'math_s10', name: 'Mathematics', code: 'MATH-10', icon: '🔢', topics: ['Real Numbers', 'Polynomials', 'Pair of Linear Equations', 'Quadratic Equations', 'Arithmetic Progressions', 'Triangles & Trigonometry', 'Statistics & Probability'] },
      { id: 'sci_s10', name: 'Science', code: 'SCI-10', icon: '🔬', topics: ['Chemical Reactions & Equations', 'Acids, Bases & Salts', 'Metals & Non-metals', 'Carbon & Compounds', 'Life Processes', 'Control & Coordination', 'Light: Reflection & Refraction', 'Electricity & Magnetism'] },
      { id: 'sst_s10', name: 'Social Science', code: 'SST-10', icon: '🌍', topics: ['Rise of Nationalism in Europe', 'Nationalism in India', 'Resources & Agriculture', 'Power Sharing & Federalism', 'Sectors of Indian Economy', 'Globalization'] },
      { id: 'eng_s10', name: 'English Language & Lit', code: 'ENG-10', icon: '📖', topics: ['Analytical Paragraph Writing', 'Formal Letters', 'Board Question Paper Mastery', 'Prose & Verse'] },
      { id: 'cs_s10', name: 'Artificial Intelligence & IT', code: 'AI-10', icon: '🤖', topics: ['Introduction to AI', 'AI Project Cycle', 'Python for AI', 'Computer Vision Basics', 'Natural Language Processing'] }
    ]
  },

  // Streams for Standard 11 & 12
  seniorStreams: [
    {
      id: 'science',
      name: 'Science Stream',
      badge: 'STEM',
      icon: '⚛️',
      description: 'Physics, Chemistry, Advanced Mathematics, Biology, and Computing for engineering & medical tracks',
      subjects: [
        { id: 'phy_s11_12', name: 'Physics', code: 'PHY-11-12', icon: '⚛️', topics: ['Units & Measurements', 'Kinematics & Dynamics', 'Work, Energy & Power', 'Rotational Motion', 'Thermodynamics', 'Electromagnetism', 'Optics & Modern Physics'] },
        { id: 'chem_s11_12', name: 'Chemistry', code: 'CHEM-11-12', icon: '🧪', topics: ['Atomic Structure', 'Chemical Bonding', 'Chemical Thermodynamics', 'Equilibrium', 'Organic Chemistry Basics', 'Coordination Compounds'] },
        { id: 'math_s11_12', name: 'Mathematics', code: 'MATH-11-12', icon: '📐', topics: ['Sets & Functions', 'Trigonometric Functions', 'Limits & Derivatives', 'Calculus (Differentiation & Integration)', 'Vectors & 3D Geometry', 'Probability'] },
        { id: 'bio_s11_12', name: 'Biology', code: 'BIO-11-12', icon: '🧬', topics: ['Diversity in Living World', 'Cell Biology', 'Plant Physiology', 'Human Physiology', 'Genetics & Evolution', 'Biotechnology'] },
        { id: 'cs_s11_12', name: 'Computer Science', code: 'CS-11-12', icon: '💻', topics: ['Python Data Structures', 'Functions & Recursion', 'File Handling', 'Relational Databases & SQL', 'Computer Networks'] },
        { id: 'eng_s11_12', name: 'English Core', code: 'ENG-11-12', icon: '📖', topics: ['Advanced Comprehension', 'Note Making & Summarizing', 'Letter & Report Writing', 'Literary Analysis'] }
      ]
    },
    {
      id: 'commerce',
      name: 'Commerce Stream',
      badge: 'FIN & BIZ',
      icon: '📈',
      description: 'Accountancy, Business Studies, Economics, Statistics, and Finance for corporate & entrepreneurial tracks',
      subjects: [
        { id: 'acc_s11_12', name: 'Accountancy', code: 'ACC-11-12', icon: '📑', topics: ['Accounting Principles & Standards', 'Recording of Transactions (Journal & Ledger)', 'Bank Reconciliation', 'Trial Balance & Depreciation', 'Financial Statements of Sole Proprietorship', 'Partnership & Company Accounts'] },
        { id: 'bst_s11_12', name: 'Business Studies', code: 'BST-11-12', icon: '🏢', topics: ['Nature & Purpose of Business', 'Forms of Business Organisations', 'Business Finance & Trade', 'Principles of Management', 'Business Environment', 'Marketing & Financial Management'] },
        { id: 'eco_s11_12', name: 'Economics', code: 'ECO-11-12', icon: '📊', topics: ['Microeconomics (Consumer & Producer Theory)', 'Macroeconomics (National Income, Money & Banking)', 'Government Budget', 'Economic Development of India'] },
        { id: 'stat_s11_12', name: 'Statistics & Applied Math', code: 'STAT-11-12', icon: '📈', topics: ['Measures of Central Tendency', 'Measures of Dispersion', 'Correlation & Index Numbers', 'Financial Mathematics', 'Linear Programming'] },
        { id: 'eng_comm_s11_12', name: 'English Core', code: 'ENG-COMM', icon: '📖', topics: ['Business Communication', 'Analytical Writing', 'Reports & Proposals', 'Literature Studies'] }
      ]
    },
    {
      id: 'arts',
      name: 'Arts & Humanities Stream',
      badge: 'HUMANITIES',
      icon: '🏛️',
      description: 'History, Political Science, Psychology, Sociology, and Philosophy for civil services, law, and journalism',
      subjects: [
        { id: 'hist_s11_12', name: 'History', code: 'HIST-11-12', icon: '🏺', topics: ['Early Civilizations', 'Themes in World History', 'Medieval & Mughal Chronicles', 'Colonialism & Freedom Struggle', 'Modern World Revolutions'] },
        { id: 'pol_s11_12', name: 'Political Science', code: 'POL-11-12', icon: '🏛️', topics: ['Political Theory & Ideologies', 'Indian Constitution at Work', 'Contemporary World Politics', 'Politics in India since Independence'] },
        { id: 'psy_s11_12', name: 'Psychology', code: 'PSY-11-12', icon: '🧠', topics: ['What is Psychology?', 'Sensory & Perceptual Processes', 'Learning & Human Memory', 'Human Development', 'Psychological Disorders & Therapy'] },
        { id: 'soc_s11_12', name: 'Sociology', code: 'SOC-11-12', icon: '👥', topics: ['Introducing Society', 'Social Structure & Stratification', 'Social Change & Order', 'Indian Society & Demographics'] },
        { id: 'geo_s11_12', name: 'Geography', code: 'GEO-11-12', icon: '🗺️', topics: ['Fundamentals of Physical Geography', 'Geomorphic Processes', 'India: Physical Environment', 'Human Geography & Settlement'] },
        { id: 'eng_arts_s11_12', name: 'English Elective', code: 'ENG-ARTS', icon: '✍️', topics: ['Creative Non-Fiction', 'Literary Criticism', 'Drama & Poetry Analysis', 'Comparative Literature'] }
      ]
    }
  ]
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { SCHOOL_CURRICULUM };
}
