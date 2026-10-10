/**
 * Learning Level Assessment Question Bank
 * Offline local questions for knowledge level evaluation
 * Smaller, focused question sets compared to onboarding assessment
 */
const LEVEL_ASSESSMENT_QUESTIONS = {
  // School Mathematics - 8-10 focused questions
  school_math: {
    subjectName: 'Mathematics',
    questions: [
      {
        id: 'lam_1',
        topic: 'Fractions & Decimals',
        difficulty: 'easy',
        question: 'What is 1/4 of 80?',
        options: ['16', '20', '25', '32'],
        correctIndex: 0,
        explanation: '1/4 of 80 = 80 ÷ 4 = 20. Wait, let me recalculate: 80 ÷ 4 = 20. So 1/4 of 80 = 20.'
      },
      {
        id: 'lam_2',
        topic: 'Algebra',
        difficulty: 'easy',
        question: 'Solve: 2x + 5 = 13',
        options: ['x = 3', 'x = 4', 'x = 6', 'x = 8'],
        correctIndex: 1,
        explanation: '2x + 5 = 13 → 2x = 8 → x = 4'
      },
      {
        id: 'lam_3',
        topic: 'Percentages',
        difficulty: 'medium',
        question: 'If a shirt costs ₹500 and there\'s a 20% discount, what is the final price?',
        options: ['₹350', '₹380', '₹400', '₹450'],
        correctIndex: 2,
        explanation: '20% of 500 = 100. Final price = 500 - 100 = ₹400'
      },
      {
        id: 'lam_4',
        topic: 'Geometry',
        difficulty: 'medium',
        question: 'What is the area of a square with side 5 cm?',
        options: ['20 cm²', '25 cm²', '30 cm²', '25 cm'],
        correctIndex: 1,
        explanation: 'Area of square = side². Area = 5² = 25 cm²'
      },
      {
        id: 'lam_5',
        topic: 'Ratios & Proportions',
        difficulty: 'medium',
        question: 'If 5 books cost ₹100, how much do 8 books cost at the same rate?',
        options: ['₹150', '₹160', '₹200', '₹240'],
        correctIndex: 1,
        explanation: 'Rate per book = 100/5 = ₹20. Cost of 8 books = 8 × 20 = ₹160'
      },
      {
        id: 'lam_6',
        topic: 'Algebraic Expressions',
        difficulty: 'hard',
        question: 'Expand: (a + b)²',
        options: ['a² + b²', 'a² + 2ab + b²', 'a² - b²', 'a + b'],
        correctIndex: 1,
        explanation: '(a + b)² = a² + 2ab + b² (algebraic identity)'
      },
      {
        id: 'lam_7',
        topic: 'Fractions & Decimals',
        difficulty: 'hard',
        question: 'Convert 3/8 to a decimal',
        options: ['0.375', '0.35', '0.285', '0.4'],
        correctIndex: 0,
        explanation: '3/8 = 3 ÷ 8 = 0.375'
      },
      {
        id: 'lam_8',
        topic: 'Data & Averages',
        difficulty: 'medium',
        question: 'What is the average of 10, 20, 30, 40?',
        options: ['20', '25', '30', '35'],
        correctIndex: 1,
        explanation: 'Average = (10 + 20 + 30 + 40) / 4 = 100 / 4 = 25'
      }
    ]
  },

  // Physics & Science - 8-10 focused questions
  physics_science: {
    subjectName: 'Physics & Science',
    questions: [
      {
        id: 'lap_1',
        topic: 'Forces & Motion',
        difficulty: 'easy',
        question: 'What is the SI unit of velocity?',
        options: ['km/h', 'm/s', 'm/hr', 'cm/s'],
        correctIndex: 1,
        explanation: 'The SI unit of velocity is meters per second (m/s)'
      },
      {
        id: 'lap_2',
        topic: 'States of Matter',
        difficulty: 'easy',
        question: 'What is the freezing point of water in Celsius?',
        options: ['0°C', '4°C', '-4°C', '100°C'],
        correctIndex: 0,
        explanation: 'Water freezes at 0°C (or 32°F)'
      },
      {
        id: 'lap_3',
        topic: 'Energy',
        difficulty: 'medium',
        question: 'Which type of energy does a moving object possess?',
        options: ['Potential Energy', 'Kinetic Energy', 'Thermal Energy', 'Chemical Energy'],
        correctIndex: 1,
        explanation: 'A moving object has kinetic energy due to its motion'
      },
      {
        id: 'lap_4',
        topic: 'Electricity',
        difficulty: 'medium',
        question: 'What is the SI unit of electric current?',
        options: ['Volt', 'Ampere', 'Ohm', 'Coulomb'],
        correctIndex: 1,
        explanation: 'The SI unit of electric current is Ampere (A)'
      },
      {
        id: 'lap_5',
        topic: 'Light & Optics',
        difficulty: 'medium',
        question: 'What is the speed of light in vacuum?',
        options: ['3 × 10⁸ m/s', '3 × 10⁶ m/s', '3 × 10¹⁰ m/s', '3 × 10⁵ m/s'],
        correctIndex: 0,
        explanation: 'The speed of light in vacuum is approximately 3 × 10⁸ m/s'
      },
      {
        id: 'lap_6',
        topic: 'Magnetism',
        difficulty: 'hard',
        question: 'Which end of a magnet is attracted to the north pole of Earth?',
        options: ['North end', 'South end', 'Both ends equally', 'Neither end'],
        correctIndex: 1,
        explanation: 'The south end (south pole) of a magnet is attracted to Earth\'s north pole'
      },
      {
        id: 'lap_7',
        topic: 'Atoms & Matter',
        difficulty: 'hard',
        question: 'What is the charge of an electron?',
        options: ['+1.6 × 10⁻¹⁹ C', '-1.6 × 10⁻¹⁹ C', '0 C', '+1.6 × 10⁻¹⁸ C'],
        correctIndex: 1,
        explanation: 'An electron has a negative charge of -1.6 × 10⁻¹⁹ Coulombs'
      },
      {
        id: 'lap_8',
        topic: 'Heat & Temperature',
        difficulty: 'medium',
        question: 'What is the boiling point of water in Celsius?',
        options: ['80°C', '90°C', '100°C', '110°C'],
        correctIndex: 2,
        explanation: 'Water boils at 100°C (or 212°F)'
      }
    ]
  },

  // Commerce & Accountancy - 8-10 questions
  commerce_accountancy: {
    subjectName: 'Accountancy & Finance',
    questions: [
      {
        id: 'lac_1',
        topic: 'Accounting Basics',
        difficulty: 'easy',
        question: 'What is the fundamental accounting equation?',
        options: [
          'Revenue - Expenses = Profit',
          'Assets = Liabilities + Owner\'s Equity',
          'Income = Expenditure',
          'Capital = Assets'
        ],
        correctIndex: 1,
        explanation: 'The fundamental accounting equation is: Assets = Liabilities + Owner\'s Equity'
      },
      {
        id: 'lac_2',
        topic: 'Debit & Credit',
        difficulty: 'easy',
        question: 'Under Double Entry System, every debit has a corresponding:',
        options: ['Loss', 'Credit', 'Expense', 'Entry'],
        correctIndex: 1,
        explanation: 'Double Entry Bookkeeping principle: every debit has an equal and opposite credit'
      },
      {
        id: 'lac_3',
        topic: 'Journal Entries',
        difficulty: 'medium',
        question: 'When cash is received from a customer, which accounts are affected?',
        options: [
          'Cash (Dr), Receivable (Cr)',
          'Cash (Dr), Revenue (Cr)',
          'Receivable (Dr), Revenue (Cr)',
          'Cash (Cr), Revenue (Dr)'
        ],
        correctIndex: 1,
        explanation: 'Cash is debited (increases) and Revenue/Income is credited'
      },
      {
        id: 'lac_4',
        topic: 'Assets & Liabilities',
        difficulty: 'medium',
        question: 'Which is a current liability?',
        options: ['Land', 'Accounts Payable', 'Building', 'Investment'],
        correctIndex: 1,
        explanation: 'Accounts Payable (money owed to suppliers within a year) is a current liability'
      },
      {
        id: 'lac_5',
        topic: 'Profit & Loss',
        difficulty: 'medium',
        question: 'If Revenue is ₹50,000 and Expenses are ₹35,000, what is Profit?',
        options: ['₹15,000', '₹25,000', '₹85,000', '₹10,000'],
        correctIndex: 0,
        explanation: 'Profit = Revenue - Expenses = 50,000 - 35,000 = ₹15,000'
      },
      {
        id: 'lac_6',
        topic: 'Depreciation',
        difficulty: 'hard',
        question: 'A machine costing ₹10,000 depreciates at 10% per annum. Value after 1 year?',
        options: ['₹9,000', '₹10,000', '₹9,500', '₹11,000'],
        correctIndex: 0,
        explanation: 'Depreciation = 10% of 10,000 = 1,000. Value = 10,000 - 1,000 = ₹9,000'
      },
      {
        id: 'lac_7',
        topic: 'Balance Sheet',
        difficulty: 'hard',
        question: 'A Balance Sheet shows the financial position at a specific:',
        options: ['Period', 'Date', 'Year', 'Month'],
        correctIndex: 1,
        explanation: 'A Balance Sheet is a snapshot of financial position at a specific date'
      },
      {
        id: 'lac_8',
        topic: 'Inventory Management',
        difficulty: 'medium',
        question: 'Opening Stock ₹1,000 + Purchases ₹5,000 - Closing Stock ₹2,000 = ?',
        options: ['₹3,000', '₹4,000', '₹6,000', '₹8,000'],
        correctIndex: 0,
        explanation: 'Cost of Goods Sold = Opening + Purchases - Closing = 1,000 + 5,000 - 2,000 = ₹4,000'
      }
    ]
  },

  // JavaScript & Web Development - 8-10 questions
  javascript_web: {
    subjectName: 'JavaScript & Web Development',
    questions: [
      {
        id: 'laj_1',
        topic: 'Variables & Data Types',
        difficulty: 'easy',
        question: 'Which keyword declares a block-scoped variable?',
        options: ['var', 'let', 'const', 'static'],
        correctIndex: 1,
        explanation: '`let` declares block-scoped variables (also `const` for immutable variables)'
      },
      {
        id: 'laj_2',
        topic: 'Functions',
        difficulty: 'easy',
        question: 'What is the output of: 2 + "2"?',
        options: ['4', '"4"', '"22"', 'Error'],
        correctIndex: 2,
        explanation: 'When adding number + string, JavaScript converts to string concatenation: "22"'
      },
      {
        id: 'laj_3',
        topic: 'Arrays',
        difficulty: 'medium',
        question: 'What does array.push() do?',
        options: [
          'Removes last element',
          'Adds element to the end',
          'Adds element to the beginning',
          'Sorts the array'
        ],
        correctIndex: 1,
        explanation: 'push() adds one or more elements to the end of an array'
      },
      {
        id: 'laj_4',
        topic: 'Objects',
        difficulty: 'medium',
        question: 'How do you access object property "name"?',
        options: ['object[name]', 'object.name', 'Both A and B', 'object:name'],
        correctIndex: 2,
        explanation: 'Both dot notation (object.name) and bracket notation (object["name"]) work'
      },
      {
        id: 'laj_5',
        topic: 'Callbacks & Promises',
        difficulty: 'medium',
        question: 'What does Promise.resolve() do?',
        options: [
          'Rejects the promise',
          'Returns a resolved promise with a value',
          'Cancels the promise',
          'Returns undefined'
        ],
        correctIndex: 1,
        explanation: 'Promise.resolve(value) returns a promise that is resolved with the value'
      },
      {
        id: 'laj_6',
        topic: 'DOM Manipulation',
        difficulty: 'hard',
        question: 'Which method selects an element by its ID?',
        options: ['querySelector()', 'getElementById()', 'getElementByClass()', 'select()'],
        correctIndex: 1,
        explanation: 'getElementById() selects an element by its ID attribute'
      },
      {
        id: 'laj_7',
        topic: 'Closures',
        difficulty: 'hard',
        question: 'What is a closure in JavaScript?',
        options: [
          'A function that closes other functions',
          'A function that has access to variables from its outer scope',
          'A way to end a program',
          'A type of loop'
        ],
        correctIndex: 1,
        explanation: 'A closure is a function bundled with references to its surrounding lexical scope'
      },
      {
        id: 'laj_8',
        topic: 'HTML & CSS',
        difficulty: 'easy',
        question: 'What does HTML stand for?',
        options: [
          'Hyper Text Markup Language',
          'High Tech Markup Language',
          'Home Tool Markup Language',
          'Hyperlinks and Text Markup Language'
        ],
        correctIndex: 0,
        explanation: 'HTML stands for HyperText Markup Language'
      }
    ]
  },

  // Python & Data Science - 8-10 questions
  python_tech: {
    subjectName: 'Python Programming',
    questions: [
      {
        id: 'lapy_1',
        topic: 'Syntax & Basics',
        difficulty: 'easy',
        question: 'What is the output of: print(type(5))?',
        options: ['<class \'int\'>', 'int', '<int>', 'number'],
        correctIndex: 0,
        explanation: 'type(5) returns <class \'int\'>, the integer type'
      },
      {
        id: 'lapy_2',
        topic: 'Data Structures',
        difficulty: 'easy',
        question: 'Which is a mutable sequence in Python?',
        options: ['Tuple', 'String', 'List', 'Set'],
        correctIndex: 2,
        explanation: 'Lists are mutable (can be modified); tuples and strings are immutable'
      },
      {
        id: 'lapy_3',
        topic: 'Loops & Control Flow',
        difficulty: 'medium',
        question: 'What does range(5) produce?',
        options: ['[0, 1, 2, 3, 4, 5]', '[0, 1, 2, 3, 4]', '[1, 2, 3, 4, 5]', '[5]'],
        correctIndex: 1,
        explanation: 'range(5) produces 0, 1, 2, 3, 4 (excludes the endpoint)'
      },
      {
        id: 'lapy_4',
        topic: 'Functions',
        difficulty: 'medium',
        question: 'What does a function without a return statement return?',
        options: ['Error', 'None', '0', 'Empty string'],
        correctIndex: 1,
        explanation: 'Functions without an explicit return statement return None'
      },
      {
        id: 'lapy_5',
        topic: 'Dictionaries',
        difficulty: 'medium',
        question: 'How do you access dictionary value for key "name"?',
        options: ['dict["name"]', 'dict.name', 'dict.get("name")', 'All of above'],
        correctIndex: 3,
        explanation: 'All three methods (bracket, dot, get) can access dictionary values'
      },
      {
        id: 'lapy_6',
        topic: 'String Methods',
        difficulty: 'hard',
        question: 'What does "hello".upper() return?',
        options: ['"HELLO"', '"Hello"', '"hELLO"', 'Error'],
        correctIndex: 0,
        explanation: 'The upper() method converts string to uppercase: "HELLO"'
      },
      {
        id: 'lapy_7',
        topic: 'List Comprehension',
        difficulty: 'hard',
        question: 'What does [x*2 for x in range(3)] produce?',
        options: ['[2, 4, 6]', '[0, 2, 4]', '[1, 2, 3]', '[2, 3, 4]'],
        correctIndex: 1,
        explanation: 'x*2 for x in range(3) = 0*2, 1*2, 2*2 = [0, 2, 4]'
      },
      {
        id: 'lapy_8',
        topic: 'Modules & Imports',
        difficulty: 'easy',
        question: 'How do you import a module named "math"?',
        options: ['include math', 'import math', 'require math', 'use math'],
        correctIndex: 1,
        explanation: 'You import a module using the `import` keyword: import math'
      }
    ]
  },

  // English Language & Communication - 8-10 questions
  english_language: {
    subjectName: 'English & Communication',
    questions: [
      {
        id: 'lae_1',
        topic: 'Grammar Basics',
        difficulty: 'easy',
        question: 'Which is a noun?',
        options: ['Run', 'Happy', 'Dog', 'Quickly'],
        correctIndex: 2,
        explanation: '"Dog" is a noun (a person, place, or thing)'
      },
      {
        id: 'lae_2',
        topic: 'Tenses',
        difficulty: 'easy',
        question: 'She _____ to the store yesterday.',
        options: ['goes', 'went', 'going', 'will go'],
        correctIndex: 1,
        explanation: '"went" is Simple Past tense, used for completed past actions'
      },
      {
        id: 'lae_3',
        topic: 'Vocabulary',
        difficulty: 'medium',
        question: 'What does "benevolent" mean?',
        options: ['Harmful', 'Kind and generous', 'Angry', 'Confused'],
        correctIndex: 1,
        explanation: '"Benevolent" means kind, generous, and charitable'
      },
      {
        id: 'lae_4',
        topic: 'Sentence Structure',
        difficulty: 'medium',
        question: 'Which is a complete sentence?',
        options: ['Running through the park', 'The cat is sleeping', 'Quickly', 'Beautiful day'],
        correctIndex: 1,
        explanation: '"The cat is sleeping" has a subject (cat) and predicate (is sleeping)'
      },
      {
        id: 'lae_5',
        topic: 'Pronouns',
        difficulty: 'medium',
        question: 'He gave the book to _____.',
        options: ['me', 'I', 'myself', 'mine'],
        correctIndex: 0,
        explanation: 'After prepositions like "to", use object pronouns: "me", not "I"'
      },
      {
        id: 'lae_6',
        topic: 'Punctuation',
        difficulty: 'hard',
        question: 'Which sentence is correctly punctuated?',
        options: [
          'She said, "im going to the store',
          'She said "Im going to the store."',
          'She said, "I\'m going to the store."',
          'she said "im going to the store"'
        ],
        correctIndex: 2,
        explanation: 'Correct: capital I, apostrophe in contraction, period inside quotes'
      },
      {
        id: 'lae_7',
        topic: 'Homonyms',
        difficulty: 'hard',
        question: 'Which word is correct: "There/Their/They\'re going to the party."',
        options: ['There', 'Their', 'They\'re', 'All are correct'],
        correctIndex: 2,
        explanation: '"They\'re" = "they are"; their = possessive; there = location'
      },
      {
        id: 'lae_8',
        topic: 'Active & Passive Voice',
        difficulty: 'medium',
        question: 'The cake was baked by Sarah. Change to active voice:',
        options: ['Sarah was baking a cake', 'Sarah baked the cake', 'The cake baked', 'Baking was done'],
        correctIndex: 1,
        explanation: 'Active: Sarah (subject) baked the cake (object)'
      }
    ]
  },

  // General Diagnostic (Fallback) - 8-10 questions
  general_diagnostic: {
    subjectName: 'General Knowledge',
    questions: [
      {
        id: 'lag_1',
        topic: 'Learning Fundamentals',
        difficulty: 'easy',
        question: 'What is the first step in learning a new concept?',
        options: [
          'Memorize all facts',
          'Understand the basic principles',
          'Take a test',
          'Skip to advanced topics'
        ],
        correctIndex: 1,
        explanation: 'Understanding basics is more important than memorizing facts'
      },
      {
        id: 'lag_2',
        topic: 'Critical Thinking',
        difficulty: 'medium',
        question: 'Which approach helps solve complex problems better?',
        options: [
          'Guessing randomly',
          'Breaking the problem into smaller parts',
          'Asking someone for the answer',
          'Ignoring difficult parts'
        ],
        correctIndex: 1,
        explanation: 'Breaking complex problems into manageable parts is an effective strategy'
      },
      {
        id: 'lag_3',
        topic: 'Study Skills',
        difficulty: 'medium',
        question: 'What is spaced repetition?',
        options: [
          'Studying the same thing many times in one day',
          'Reviewing material at increasing intervals over time',
          'Forgetting and then relearning',
          'Reading without taking notes'
        ],
        correctIndex: 1,
        explanation: 'Spaced repetition: reviewing at intervals helps long-term memory retention'
      },
      {
        id: 'lag_4',
        topic: 'General Knowledge',
        difficulty: 'easy',
        question: 'What is the capital of France?',
        options: ['Lyon', 'Paris', 'Marseille', 'Nice'],
        correctIndex: 1,
        explanation: 'Paris is the capital and largest city of France'
      },
      {
        id: 'lag_5',
        topic: 'Logical Reasoning',
        difficulty: 'medium',
        question: 'If all cats are animals and Fluffy is a cat, then:',
        options: [
          'Fluffy is not an animal',
          'Fluffy is an animal',
          'Fluffy might be an animal',
          'We cannot determine'
        ],
        correctIndex: 1,
        explanation: 'Logical deduction: if A implies B, and we have A, then B must be true'
      },
      {
        id: 'lag_6',
        topic: 'Problem Solving',
        difficulty: 'hard',
        question: 'Which is NOT a problem-solving technique?',
        options: [
          'Trial and error',
          'Working backwards',
          'Brainstorming solutions',
          'Avoiding the problem'
        ],
        correctIndex: 3,
        explanation: 'Avoiding problems doesn\'t solve them; active strategies are better'
      },
      {
        id: 'lag_7',
        topic: 'Knowledge Application',
        difficulty: 'hard',
        question: 'Why is practical application important in learning?',
        options: [
          'It wastes time',
          'It helps convert knowledge into skills',
          'It confuses learners',
          'It has no effect'
        ],
        correctIndex: 1,
        explanation: 'Applying knowledge converts abstract learning into practical skills'
      },
      {
        id: 'lag_8',
        topic: 'Continuous Learning',
        difficulty: 'medium',
        question: 'What should you do after making a mistake?',
        options: [
          'Ignore it',
          'Feel bad and quit',
          'Learn from it and improve',
          'Blame others'
        ],
        correctIndex: 2,
        explanation: 'Learning from mistakes is essential for growth and improvement'
      }
    ]
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { LEVEL_ASSESSMENT_QUESTIONS };
}
